<?php

namespace App\Jobs;

use App\Models\Certification;
use App\Models\EnviaEmailParametro;
use App\Models\JobLog; // Importando a model para salvar os logs
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\Parametro;

class MonitorCertificadosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        $this->logMessage('Iniciando o MonitorCertificadosJob');

        // Verifica se o monitoramento está ativado
        $parametros = EnviaEmailParametro::where('status', true)->first();

        if (!$parametros) {
            $this->logMessage('Monitoramento de certificados não está ativado.');
            return;
        }

        // Buscar o valor do parâmetro 'dias_para_vencer'
        $daysUntilWarning = Parametro::where('dias_faltantes', 'dias_para_vencer')->value('valor');
        if (is_null($daysUntilWarning)) {
            $daysUntilWarning = 10; // Valor padrão caso o parâmetro não esteja definido
        } else {
            $daysUntilWarning = (int) $daysUntilWarning; // Certifique-se de que é um inteiro
        }

        // Consulta todos os certificados que estão vencidos ou próximos da data de vencimento
        $certificates = Certification::whereRaw('(DATEDIFF(validTo_time_t, NOW()) <= ?)', [$daysUntilWarning])->get();

        $this->logMessage('Certificados encontrados: ' . $certificates->count());

        foreach ($certificates as $certificate) {
            // Verifica se o certificado possui um e-mail cadastrado
            if (empty($certificate->email)) {
                $this->logMessage('Certificado sem e-mail cadastrado: ' . $certificate->name);
                continue; // Pula para o próximo certificado sem processar
            }

            // Tratamento do nome
            preg_match('/CN=(.*?):\d+/', $certificate->name, $matches);
            $cleanName = $matches[1] ?? 'Nome Indisponível';

            $validTo = Carbon::parse($certificate->validTo_time_t);
            $daysUntilExpiry = Carbon::now()->diffInDays($validTo, false); // False para considerar dias negativos

            $message = '';
            if ($daysUntilExpiry > 0) {
                $message = "O certificado {$cleanName} está próximo da expiração. Válido até {$validTo->format('d/m/Y')}.";
                $this->sendEmailNotification($parametros, $certificate, $message);
                $this->sendWhatsAppNotification($certificate, $message);
            } elseif ($daysUntilExpiry === 0 || ($daysUntilExpiry < 0 && !$certificate->email_expirado_enviado)) {
                $message = $daysUntilExpiry === 0
                    ? "O certificado {$cleanName} expira hoje ({$validTo->format('d/m/Y')})."
                    : "O certificado {$cleanName} expirou em {$validTo->format('d/m/Y')}.";
                $this->sendEmailNotification($parametros, $certificate, $message);
                $this->sendWhatsAppNotification($certificate, $message);

                // Marcar o certificado como "email de expiração enviado"
                $certificate->email_expirado_enviado = true;
                $certificate->save();
            }
            $this->logMessage('Enviando e-mail para: ' . $certificate->email, $certificate->email, $certificate->name, $certificate->numero, $parametros->instancia);
        }
    }

    public function sendEmailNotification($parametros, $certificado, $mensagem)
    {
        // Verifica se o certificado possui um e-mail cadastrado
        if (empty($certificado->email)) {
            $this->logMessage('Certificado sem e-mail cadastrado: ' . $certificado->name);
            return;
        }

        // Tratamento do nome para extrair o nome limpo
        preg_match('/CN=(.*?):\d+/', $certificado->name, $matches);
        $cleanName = $matches[1] ?? 'Nome Indisponível';

        $this->logMessage('Enviando e-mail com assunto: ' . $parametros->titulo, $certificado->email, $cleanName);

        Mail::send('emails.certificado_notificacao', ['certificado' => $certificado, 'mensagem' => $mensagem, 'cleanName' => $cleanName], function ($message) use ($parametros, $certificado) {
            $message->to($certificado->email)
                    ->subject($parametros->titulo)
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        });
    }

    public function sendWhatsAppNotification($certificado, $mensagem)
    {
        // Verifica se o certificado possui um número cadastrado
        if (empty($certificado->numero)) {
            $this->logMessage('Certificado sem número cadastrado: ' . $certificado->name);
            return;
        }

        // Obtém o nome da instância
        $instanceName = Parametro::where('dias_faltantes', 'envia_mensagens_wp')->value('instancia');

        if (is_null($instanceName)) {
            $this->logMessage('Nome da instância não encontrado para envio via WhatsApp.');
            return;
        }

        // Cria o conteúdo da mensagem em texto simples
        $messageContent = $this->generateWhatsAppMessage($certificado->name, $mensagem);

        // Prepara o payload para a API do WhatsApp
        $payload = [
            'number' => $certificado->numero,
            'options' => [
                'delay' => 1200,
                'presence' => 'composing',
                'linkPreview' => false,
            ],
            'textMessage' => [
                'text' => $messageContent,
            ],
        ];

        // Envia a requisição para a API do WhatsApp
        $url = "http://evolution_api:8080/message/sendText/{$instanceName}";

        try {
            $response = Http::withHeaders(['apikey' => env('AUTHENTICATION_API_KEY')])->post($url, $payload);

            if ($response->successful()) {
                $this->logMessage('Notificação via WhatsApp enviada para: ' . $certificado->numero, $certificado->numero, $certificado->name, null, $instanceName);
            } else {
                $this->logMessage('Erro ao enviar notificação via WhatsApp para: ' . $certificado->numero, $certificado->numero, $certificado->name, null, $instanceName);
            }
        } catch (\Exception $e) {
            $this->logMessage('Erro ao enviar notificação via WhatsApp: ' . $e->getMessage(), $certificado->numero, $certificado->name, null, $instanceName);
        }

        // Intervalo de 30 segundos antes do próximo envio
        sleep(30);
    }

    protected function generateWhatsAppMessage($certificadoName, $mensagem)
    {
        // Extrai o nome limpo do certificado
        preg_match('/CN=(.*?):\d+/', $certificadoName, $matches);
        $cleanName = $matches[1] ?? 'Nome Indisponível';

        // Gera o conteúdo da mensagem em texto simples
        return "Olá, {$cleanName},\n\n{$mensagem}\n\nAtenciosamente,\nAWN Soluções Contábeis";
    }

    protected function logMessage($message, $email = null, $certificado = null, $numero = null, $instancia = null)
    {
        // Salva o log na tabela job_logs
        JobLog::create([
            'message' => $message,
            'email' => $email,
            'certificado' => $certificado,
            'numero' => $numero,
            'instancia' => $instancia,
        ]);

        // Também envia para o arquivo de log
        Log::info($message);
    }
}
