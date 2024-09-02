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
    $certificates = Certification::whereRaw('(DATEDIFF(validTo_time_t, NOW()) <= ?)', [$daysUntilWarning])
                                 ->get();

    $this->logMessage('Certificados encontrados: ' . $certificates->count());

    foreach ($certificates as $certificate) {
        // Tratamento do nome
        preg_match('/CN=(.*?):\d+/', $certificate->name, $matches);
        $cleanName = $matches[1] ?? 'Nome Indisponível';

        $validTo = Carbon::parse($certificate->validTo_time_t);
        $daysUntilExpiry = Carbon::now()->diffInDays($validTo, false); // False para considerar dias negativos

        $message = '';
        if ($daysUntilExpiry > 0) {
            $message = "O certificado {$cleanName} está próximo da expiração. Válido até {$validTo->format('d/m/Y')}.";
        } elseif ($daysUntilExpiry === 0) {
            $message = "O certificado {$cleanName} expira hoje ({$validTo->format('d/m/Y')}).";
        } else {
            $message = "O certificado {$cleanName} expirou em {$validTo->format('d/m/Y')}.";
        }

        $this->logMessage('Enviando e-mail para: ' . $certificate->email, $certificate->email, $cleanName);

        // Envia e-mail de notificação
        $this->sendEmailNotification($parametros, $certificate, $message);
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

    protected function logMessage($message, $email = null, $certificado = null)
    {
        // Salva o log na tabela job_logs
        JobLog::create([
            'message' => $message,
            'email' => $email,
            'certificado' => $certificado,
        ]);

        // Também envia para o arquivo de log
        Log::info($message);
    }
}
