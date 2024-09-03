<?php
// app/Http/Controllers/ControlePontoController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ControlePontoController extends Controller
{
    public function showForm()
    {
        return view('folha.folha-ponto');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'diaSemana' => 'required|string',
            'data' => 'required|date',
            'entrada' => 'required|date_format:H:i',
            'saida' => 'required|date_format:H:i',
            'total' => 'nullable|numeric',
            'atraso' => 'nullable|numeric',
            'extras' => 'nullable|numeric',
            'horasExtrasNoturnas' => 'nullable|numeric',
            'adicionalNoturno' => 'nullable|numeric',
            'obs' => 'nullable|string',
        ]);

        // Calcular totais e valores
        $entrada = new \DateTime($data['entrada']);
        $saida = new \DateTime($data['saida']);
        $totalHoras = ($saida->getTimestamp() - $entrada->getTimestamp()) / 3600;

        // Preencher os campos adicionais
        $data['total'] = $totalHoras;

        // Salvando os dados no banco de dados
        Ponto::create($data);

        return response()->json(['message' => 'Dados recebidos e salvos com sucesso!']);
    }
}
