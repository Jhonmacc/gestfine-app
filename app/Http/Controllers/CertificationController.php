<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certification;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CertificationController extends Controller
{
    public function index(Request $request)
    {
        $certificates = Certification::all();

        $status = $request->input('status', 'Todos');

        if ($status !== 'Todos') {
            $certificates = $certificates->filter(function ($certificate) use ($status) {
                $validTo = Carbon::parse($certificate->validTo_time_t);
                $daysUntilExpiry = $validTo->diffInDays(Carbon::now(), false);

                switch ($status) {
                    case 'No Prazo':
                        return $daysUntilExpiry > 30;
                    case 'Perto de Vencer':
                        return $daysUntilExpiry > 0 && $daysUntilExpiry <= 30;
                    case 'Vencido':
                        return $daysUntilExpiry <= 0;
                    default:
                        return true;
                }
            });
        }

        $totalCertificates = $certificates->count();
        $validCertificates = $certificates->where('validTo_time_t', '>=', now())->count();
        $expiredCertificates = $certificates->where('validTo_time_t', '<', now())->count();
        $nearExpiration = $certificates->filter(function ($certificate) {
            $expirationDate = strtotime($certificate->validTo_time_t);
            $daysToExpire = ($expirationDate - time()) / (60 * 60 * 24);
            return $daysToExpire > 0 && $daysToExpire <= 30;
        })->count();

        return view('certification.index', compact('certificates', 'totalCertificates', 'validCertificates', 'expiredCertificates', 'nearExpiration'));
    }

    public function getChartData()
    {
        $certificates = Certification::all();

        $withinDeadline = 0;
        $nearExpiration = 0;
        $expired = 0;
        $societarioCount = 0;
        $nonSocietarioCount = 0;

        foreach ($certificates as $certificate) {
            $validTo = strtotime($certificate->validTo_time_t);
            $daysUntilExpiry = ceil(($validTo - time()) / (60 * 60 * 24));

            if ($daysUntilExpiry > 0) {
                $withinDeadline++;
            } elseif ($daysUntilExpiry > -10) {
                $nearExpiration++;
            } else {
                $expired++;
            }

            if (!empty($certificate->societario)) {
                $societarioCount++;
            } else {
                $nonSocietarioCount++;
            }
        }

        return response()->json([
            'statusData' => [$withinDeadline, $nearExpiration, $expired],
            'societarioData' => [$societarioCount, $nonSocietarioCount]
        ]);
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
