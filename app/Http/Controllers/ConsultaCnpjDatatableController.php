<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConsultaCnpjDatatableController extends Controller
{
    public function consultar($cnpj)
    {
        // Validar o CNPJ enviado na URL
        $validator = \Validator::make(['cnpj' => $cnpj], [
            'cnpj' => 'required|regex:/^[0-9]{14}$/', // Validação de 14 dígitos
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'CNPJ inválido. O CNPJ deve ter 14 dígitos numéricos.'
            ], 422);
        }

        try {
            // Fazer a chamada para a API ReceitaWS
            $response = Http::get("https://receitaws.com.br/v1/cnpj/$cnpj");

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                // Mapeia os códigos de erro para mensagens apropriadas
                $errorMessages = [
                    400 => 'Requisição inválida',
                    401 => 'Não autorizado',
                    403 => 'Acesso negado',
                    404 => 'Não encontrado',
                    429 => 'Excesso de pedidos',
                    500 => 'Erro interno',
                ];

                // Retorna o erro com base no código
                $errorMessage = $errorMessages[$response->status()] ?? 'Erro desconhecido';
                return response()->json(['error' => $errorMessage], $response->status());
            }

        } catch (\Exception $e) {
            Log::error('Erro ao consultar CNPJ na ReceitaWS: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno'], 500);
        }
    }

}
