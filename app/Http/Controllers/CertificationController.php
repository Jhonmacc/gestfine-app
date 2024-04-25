<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certification;

class CertificationController extends Controller
{
    public function index()
    {
        // Recupera todos os certificados do banco de dados
        $certificates = Certification::all();

        // Passa os certificados para a view
        return view('certification.index', compact('certificates'));
    }

    public function getChartData()
{
    $certificates = Certification::all();

    $withinDeadline = 0;
    $nearExpiration = 0;
    $expired = 0;

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
    }

    return response()->json([
        'labels' => ['Dentro do prazo', 'Perto de vencer', 'Vencidos'],
        'data' => [$withinDeadline, $nearExpiration, $expired]
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

        // Tenta ler o certificado
        if (!openssl_pkcs12_read($pfxContent, $x509certdata, $certPassword)) {

            return back()->withErrors('O certificado não pode ser lido ou a senha está incorreta!');
        }

        if (empty($x509certdata)) {

            return back()->withErrors('A senha do certificado está incorreta!');
        }

        // Descriptografa e processa o certificado
        $certInfo = openssl_x509_parse(openssl_x509_read($x509certdata['cert']));

        // Salva os dados do certificado no banco de dados
        $certification = new Certification();
        $certification->name = $certInfo['name'];
        $certification->validTo_time_t = date('Y-m-d', $certInfo['validTo_time_t']);
        $certification->cnpj_cpf = $certInfo['subject']['CN'];
        $certification->societario = $societario;
        $certification->save();

        return redirect()->route('certification.index')->with('success', 'Certificado salvo com sucesso!');
    }

    public function destroy($id)
    {
        $certification = Certification::findOrFail($id);
        $certName = $certification->name;

        $certification->delete();

        return response()->json(['success' => true, 'message' => "O Certificado {$certName} foi excluído com sucesso!"]);
    }
}
