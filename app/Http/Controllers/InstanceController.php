<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InstanceController extends Controller
{
    public function showForm()
    {
        return view('instance.create');
    }

    public function createInstance(Request $request)
    {
        // Validação dos dados recebidos
        $request->validate([
            'instanceName' => 'required|string',
            'token' => 'required|string',
        ]);

        // Dados a serem enviados na requisição
        $data = [
            'instanceName' => $request->input('instanceName'),
            'token' => $request->input('token'),
        ];

        // Enviando a requisição para a API externa
        $response = Http::withHeaders([
            'apikey' => 'J6P756FCDA4D4FD5936555990E718741'
        ])->post('http://evolution_api:8080/instance/create', $data);

        // Verificando o status da resposta e retornando JSON
        if ($response->successful()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Instância criada com sucesso!',
                'data' => $response->json(),
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao criar instância',
                'error' => $response->json(),
            ], $response->status());
        }
    }
}
