<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InstanceController extends Controller
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('AUTHENTICATION_API_KEY');
    }

    public function showForm()
    {
        return view('instance.create');
    }

    public function sendMessageApi()
    {
        return view('instance.sendMessageApi');
    }

    public function createInstance(Request $request)
    {
        $this->validateInstanceRequest($request);

        $data = $this->buildRequestData($request);

        return $this->sendPostRequest('http://evolution_api:8080/instance/create', $data, 'Instância criada com sucesso!');
    }

    public function fetchInstances()
    {
        return $this->sendGetRequest('http://evolution_api:8080/instance/fetchInstances', 'Erro ao buscar instâncias');
    }

    public function deleteAndLogoutInstance($instanceName)
    {
        // Primeiro, buscar o status da instância
        $statusResponse = Http::withHeaders($this->getHeaders())->get("http://evolution_api:8080/instance/connectionState/{$instanceName}");

        if (!$statusResponse->successful()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao buscar o status da instância.',
                'error' => $statusResponse->json()
            ], $statusResponse->status());
        }

        $statusData = $statusResponse->json();
        $instanceStatus = $statusData['instance']['state'] ?? 'unknown';

        // Se a instância estiver "close" ou "connecting", pular o processo de logout
        if ($instanceStatus !== 'open') {
            return $this->deleteInstance($instanceName);
        }

        // Se a instância estiver "open", realizar o logout antes de deletar
        $logoutResponse = Http::withHeaders($this->getHeaders())->delete("http://evolution_api:8080/instance/logout/{$instanceName}");

        if (!$logoutResponse->successful()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao fazer logout da instância.',
                'error' => $logoutResponse->json()
            ], $logoutResponse->status());
        }

        // Excluir a instância após o logout bem-sucedido
        return $this->deleteInstance($instanceName);
    }

    private function deleteInstance($instanceName)
    {
        $deleteResponse = Http::withHeaders($this->getHeaders())->delete("http://evolution_api:8080/instance/delete/{$instanceName}");

        if ($deleteResponse->successful()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Instância deletada com sucesso!',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao deletar a instância.',
                'error' => $deleteResponse->json()
            ], $deleteResponse->status());
        }
    }

    private function sendPostRequest(string $url, array $data, string $successMessage)
    {
        try {
            $response = Http::withHeaders($this->getHeaders())->post($url, $data);
            return $this->handleApiResponse($response, $successMessage);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Erro ao criar instância');
        }
    }

    private function sendMessagePostRequest(string $url, array $data, string $successMessage)
    {
        try {
            $response = Http::withHeaders($this->getHeaders())->post($url, $data);
            return $this->handleApiResponse($response, $successMessage);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Erro ao enviar mensagem');
        }
    }

    private function sendGetRequest(string $url, string $errorMessage)
    {
        try {
            $response = Http::withHeaders($this->getHeaders())->get($url);
            return $this->handleApiResponse($response, $errorMessage);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Erro ao processar a requisição');
        }
    }

    private function sendDeleteRequest(string $url, string $successMessage, string $errorMessage)
    {
        try {
            $response = Http::withHeaders($this->getHeaders())->delete($url);
            return $this->handleApiResponse($response, $successMessage, $errorMessage);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Erro ao processar a requisição');
        }
    }

    public function sendMessage(Request $request)
{
    // Validação
    $request->validate([
        'instanceopen' => 'required|string',
        'number' => 'required|array',
        'number.*' => 'string',
        'options' => 'required|array',
        'options.delay' => 'required|integer',
        'options.presence' => 'required|string',
        'options.linkPreview' => 'required|boolean',
        'textMessage' => 'required|array',
        'textMessage.text' => 'required|string',
    ]);

    $instanceOpen = $request->input('instanceopen'); // Nome da instância
    $numbers = $request->input('number');
    $options = $request->input('options');
    $textMessage = $request->input('textMessage');

    $failureNumbers = [];
    $successCount = 0;

    foreach ($numbers as $number) {
        $url = "http://evolution_api:8080/message/sendText/{$instanceOpen}";

        try {
            $data = [
                'number' => $number, // Número individual para o payload
                'options' => $options,
                'textMessage' => $textMessage,
            ];

            $response = Http::withHeaders($this->getHeaders())->post($url, $data);

            // Log da resposta para depuração
            \Log::info('API Response:', ['response' => $response->json()]);

            if ($response->successful()) {
                $successCount++;
            } else {
                $failureNumbers[] = $number;
            }
        } catch (\Exception $e) {
            \Log::error('Error sending message:', ['exception' => $e->getMessage()]);
            $failureNumbers[] = $number;
        }
    }

    $total = count($numbers);
    $percentage = ($total > 0) ? ($successCount / $total) * 100 : 0;

    $status = $successCount === $total ? 'success' : 'error';
    $message = $successCount === $total
        ? 'Todas as mensagens foram enviadas com sucesso!'
        : 'Algumas mensagens falharam ao serem enviadas.';

    return response()->json([
        'status' => $status,
        'message' => $message,
        'percentage' => $percentage,
        'failureNumbers' => $failureNumbers,
    ]);
}


    private function getHeaders(): array
    {
        return [
            'apikey' => $this->apiKey,
        ];
    }

    private function buildRequestData(Request $request): array
    {
        return [
            'instanceName' => $request->input('instanceName'),
            'token' => $request->input('token'),
        ];
    }

    private function validateInstanceRequest(Request $request)
    {
        $request->validate([
            'instanceName' => 'required|string',
            'token' => 'required|string',
        ]);
    }

    private function handleApiResponse($response, string $successMessage, string $errorMessage = 'Erro ao processar a requisição')
    {
        if ($response->successful()) {
            return response()->json([
                'status' => 'success',
                'message' => $successMessage,
                'data' => $response->json(),
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => $errorMessage,
                'error' => $response->json(),
            ], $response->status());
        }
    }
    private function handleException(\Exception $e, string $message)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'error' => $e->getMessage(),
        ], 500);
    }
}
