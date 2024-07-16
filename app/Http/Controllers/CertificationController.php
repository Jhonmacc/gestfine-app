<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certification;
use App\Models\Parametro;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CertificationController extends Controller
{
	public function index(Request $request)
{
    // Buscar o valor do parâmetro 'dias_para_vencer'
    $daysUntilWarning = Parametro::where('dias_faltantes', 'dias_para_vencer')->value('valor');
    if (is_null($daysUntilWarning)) {
        $daysUntilWarning = 10; // Valor padrão caso o parâmetro não esteja definido
    } else {
        $daysUntilWarning = (int) $daysUntilWarning; // Certifique-se de que é um inteiro
    }

    $certificates = Certification::all();

    // Inicializar contadores
    $totalCertificates = $certificates->count();
    $withinDeadline = 0;
    $nearExpiration = 0;
    $expired = 0;
    $cpfCount = 0;
    $cnpjCount = 0;

    // Contar certificados válidos, vencidos e próximos de vencer
    foreach ($certificates as $certificate) {
        $validTo = strtotime($certificate->validTo_time_t);
        $daysUntilExpiry = ceil(($validTo - time()) / (60 * 60 * 24));

        if ($daysUntilExpiry > $daysUntilWarning) {
            $withinDeadline++;
        } elseif ($daysUntilExpiry > 0) {
            $nearExpiration++;
        } else {
            $expired++;
        }

        // Verificar se é CPF ou CNPJ
        $cnpjCpf = $certificate->cnpj_cpf;
        if (strlen($cnpjCpf) == 11) {
            $cpfCount++;
        } elseif (strlen($cnpjCpf) == 14) {
            $cnpjCount++;
        }
    }

    $status = $request->input('status', 'Todos');

    if ($status !== 'Todos') {
        $certificates = $certificates->filter(function ($certificate) use ($status, $daysUntilWarning) {
            $validTo = Carbon::parse($certificate->validTo_time_t);
            $daysUntilExpiry = $validTo->diffInDays(Carbon::now(), false);

            switch ($status) {
                case 'No Prazo':
                    return $daysUntilExpiry > $daysUntilWarning;
                case 'Perto de Vencer':
                    return $daysUntilExpiry > 0 && $daysUntilExpiry <= $daysUntilWarning;
                case 'Vencido':
                    return $daysUntilExpiry <= 0;
                default:
                    return true;
            }
        });
    }

    return view('certification.index', compact('certificates', 'totalCertificates', 'withinDeadline', 'expired', 'nearExpiration', 'daysUntilWarning', 'cpfCount', 'cnpjCount'));
}


public function getChartData()
{
    // Buscar o valor do parâmetro 'dias_para_vencer'
    $daysUntilWarning = Parametro::where('dias_faltantes', 'dias_para_vencer')->value('valor');
    if (is_null($daysUntilWarning)) {
        $daysUntilWarning = 10; // Valor padrão caso o parâmetro não esteja definido
    } else {
        $daysUntilWarning = (int) $daysUntilWarning; // Certifique-se de que é um inteiro
    }

    $certificates = Certification::all();

    $withinDeadline = 0;
    $nearExpiration = 0;
    $expired = 0;
    $cpfCount = 0;
    $cnpjCount = 0;

    foreach ($certificates as $certificate) {
        $validTo = strtotime($certificate->validTo_time_t);
        $daysUntilExpiry = ceil(($validTo - time()) / (60 * 60 * 24));

        if ($daysUntilExpiry > $daysUntilWarning) {
            $withinDeadline++;
        } elseif ($daysUntilExpiry > 0) {
            $nearExpiration++;
        } else {
            $expired++;
        }

        // Extrair apenas os números do campo cnpj_cpf
        $number = preg_replace('/[^0-9]/', '', $certificate->cnpj_cpf);

        // Determinar se é CPF ou CNPJ
        if (strlen($number) === 11 && is_numeric($number)) {
            $cpfCount++;
        } elseif (strlen($number) === 14 && is_numeric($number)) {
            $cnpjCount++;
        }
    }

    return response()->json([
        'statusData' => [$withinDeadline, $nearExpiration, $expired],
        'cpfCount' => $cpfCount,
        'cnpjCount' => $cnpjCount,
    ]);
}


// Função para verificar se é CPF
private function isCpf($value)
{
    return strlen($value) === 11 && is_numeric($value);
}

// Função para verificar se é CNPJ
private function isCnpj($value)
{
    return strlen($value) === 14 && is_numeric($value);
}


    public function validateCertification(Request $request)
    {
        $request->validate([
            'certificate' => 'required|file',
            'password' => 'required|string',
            'societario' => 'nullable|string',
        ]);

        // Captura o arquivo e a senha do certificado do request
        $certificateFile = $request->file('certificate');
        $certPassword = $request->input('password');
        $societario = $request->input('societario');

        // Lê o conteúdo do arquivo do certificado
        $pfxContent = file_get_contents($certificateFile->getPathName());
        $fileName = 'certificate_' . time() . '.pfx';
        // Tenta ler o certificado
        if (!openssl_pkcs12_read($pfxContent, $x509certdata, $certPassword)) {
            return back()->withErrors('O certificado não pode ser lido ou a senha está incorreta!');
        }

        if (empty($x509certdata)) {
            return back()->withErrors('A senha do certificado está incorreta!');
        }

        // Descriptografa e processa o certificado
        $certInfo = openssl_x509_parse(openssl_x509_read($x509certdata['cert']));
         // Verifica se já existe um certificado com o mesmo CNPJ/CPF
        $cnpjCpf = $certInfo['subject']['CN'];
        if (Certification::where('cnpj_cpf', $cnpjCpf)->exists()) {
            return back()->withErrors('Já existe um certificado com este CNPJ/CPF cadastrado!');
    }
        // Salva o arquivo no sistema de arquivos do servidor
        $filePath = $certificateFile->storeAs('certificates', $fileName, 'public'); // Salva na pasta storage/app/public/certificates criando um link simbólico
        // Salva os dados do certificado no banco de dados
        $certification = new Certification();
        $certification->name = $certInfo['name'];
        $certification->validTo_time_t = date('Y-m-d', $certInfo['validTo_time_t']);
        $certification->cnpj_cpf = $certInfo['subject']['CN'];
        $certification->societario = $societario;
        $certification->certificate_path = $filePath; // Armazena o caminho do arquivo
        $certification->senhas = $certPassword; // Armazena a senha
        $certification->save();

        return redirect()->route('certification.index')->with('success', 'Certificado salvo com sucesso!');
    }
    public function validatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
            'id' => 'required|integer|exists:certifications,id',
        ]);

        $certificate = Certification::find($request->id);

        // Lê o conteúdo do arquivo do certificado
        $pfxContent = Storage::disk('public')->get($certificate->certificate_path);

        // Tenta ler o certificado com a nova senha
        if (openssl_pkcs12_read($pfxContent, $x509certdata, $request->password)) {
            // Atualiza a senha no banco de dados
            $certificate->senhas = $request->password;
            $certificate->save();

            return response()->json(['valid' => true]);
        } else {
            return response()->json(['valid' => false]);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'certificate' => 'required|file',
            'password' => 'required|string',
            'societario' => 'nullable|string',
        ]);

        $certificate = Certification::findOrFail($id);

        // Captura o arquivo e a senha do certificado do request
        $certificateFile = $request->file('certificate');
        $certPassword = $request->input('password');
        $societario = $request->input('societario');

        // Lê o conteúdo do arquivo do certificado
        $pfxContent = file_get_contents($certificateFile->getPathName());
        $fileName = 'certificate_' . time() . '.pfx';

        // Tenta ler o certificado
        if (!openssl_pkcs12_read($pfxContent, $x509certdata, $certPassword)) {
            return back()->withErrors('O certificado não pode ser lido ou a senha está incorreta!');
        }

        if (empty($x509certdata)) {
            return back()->withErrors('A senha do certificado está incorreta!');
        }

        // Descriptografa e processa o certificado
        $certInfo = openssl_x509_parse(openssl_x509_read($x509certdata['cert']));

        // Deleta o certificado antigo
        if (Storage::disk('public')->exists($certificate->certificate_path)) {
            Storage::disk('public')->delete($certificate->certificate_path);
        }

        // Salva o novo arquivo no sistema de arquivos do servidor
        $filePath = $certificateFile->storeAs('certificates', $fileName, 'public'); // Salva na pasta storage/app/public/certificates criando um link simbólico

        // Atualiza os dados do certificado no banco de dados
        $certificate->name = $certInfo['name'];
        $certificate->validTo_time_t = date('Y-m-d', $certInfo['validTo_time_t']);
        $certificate->cnpj_cpf = $certInfo['subject']['CN'];
        $certificate->societario = $societario;
        $certificate->certificate_path = $filePath; // Armazena o caminho do arquivo
        $certificate->senhas = $certPassword; // Armazena a senha
        $certificate->save();

        return redirect()->route('certification.index')->with('success', 'Certificado atualizado com sucesso!');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            // outros campos validados
            'password' => 'required|string',
        ]);

        $certification = new Certification();
        // outros campos sendo atribuídos
        $certification->password = $request->input('password');
        $certification->save();

        return redirect()->route('certifications.index')->with('success', 'Certificado cadastrado com sucesso!');
    }

    public function download($id)
    {
        $certification = Certification::findOrFail($id);
        if ($certification && Storage::disk('public')->exists($certification->certificate_path)) {
            // Obtém o caminho real no disco para o arquivo
            $filePath = Storage::disk('public')->path($certification->certificate_path);
            // Define os cabeçalhos para o tipo correto e força o download com o nome original do arquivo .pfx
            $headers = [
                'Content-Type' => 'application/x-pkcs12',
                'Content-Disposition' => 'attachment; filename="' . basename($certification->certificate_path) . '"'
            ];
            // Retorna o arquivo para download
            return response()->download($filePath, basename($certification->certificate_path), $headers);
        } else {
            return back()->withErrors('Arquivo não encontrado!');
        }
    }

    public function destroy($id)
    {
        $certification = Certification::findOrFail($id);
        $certName = $certification->name;

        // Verifica se o arquivo existe e então o exclui
        if ($certification->certificate_path && Storage::disk('public')->exists($certification->certificate_path)) {
            Storage::disk('public')->delete($certification->certificate_path);
        }

        // Exclui o registro do banco de dados
        $certification->delete();

        return response()->json(['success' => true, 'message' => "O Certificado {$certName} foi excluído com sucesso!"]);
    }
}
