<?php

use App\Http\Controllers\CertificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ParametroController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InstanceController;
use App\Http\Controllers\NumberController;

Route::get('/', [LoginController::class, 'welcome']);
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Rotas que requerem verificação do usuário ativo
    Route::middleware(['active.user'])->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard.index');
        })->name('dashboard.index');
    });

    // Registro de usuários
    // Route::get('/register', function () {
    //     return view('auth.register');
    // })->name('register');
    //

    // Gerenciamento de Usuários
    // Route::resource('parametros', ParametroController::class)->except(['show']);
    Route::resource('users', UserController::class)->except(['create', 'edit', 'show']);

    // Rota para o form criar instancias
    route::get('/instance/create', [InstanceController::class, 'showForm'])->name('instance.create');
    route::get('/instance/send-message-api', [InstanceController::class, 'sendMessageApi'])->name('instance.sendMessageApi');
    Route::post('/create-instance', [InstanceController::class, 'createInstance'])->name('instance.createInstance');
    Route::get('/instance/fetchInstances', [InstanceController::class, 'fetchInstances']);
    route::post('/send-message', [InstanceController::class, 'sendMessage']);
    route::delete('/instance/deleteAndLogout/{instanceName}', [InstanceController::class, 'deleteAndLogoutInstance']);
    // Rotas de Mensagems Whatsapp
    Route::get('/message-events', [CertificationController::class, 'showMessageEvents'])->name('certification.message-events');
    // Rotas add números de telefone
    Route::post('/certification/update-number', [CertificationController::class, 'updateNumber']);

    // Rotas de Controle de Certificados
    Route::get('/numbers', [NumberController::class, 'getNumbers']);
    Route::get('/dashboard/chart-data', [CertificationController::class, 'getChartData'])->name('dashboard.chartData');
    Route::get('/certification', [CertificationController::class, 'index'])->name('certification.index');
    Route::get('/certification/download/{id}', [CertificationController::class, 'download'])->name('certification.download');
    Route::post('/certification/validate', [CertificationController::class, 'validateCertification'])->name('certification.validate');

    // Rotas Substituir Certificados
    Route::post('/certification/validate-password', [CertificationController::class, 'validatePassword'])->name('certification.validatePassword');
    Route::put('/certification/{id}/update', [CertificationController::class, 'update'])->name('certification.update');

    // Rotas de Parâmetros
    Route::get('/parametros/edit', [ParametroController::class, 'editParameters'])->name('parametros.edit-parameters');
    Route::get('/parametros', [ParametroController::class, 'editParameters'])->name('parametros.editParameters');
    Route::get('/parametros/create', [ParametroController::class, 'create'])->name('parametros.create');
    Route::post('/parametros/create', [ParametroController::class, 'store'])->name('parametros.store');
    Route::post('/parametros/update', [ParametroController::class, 'updateParameters'])->name('parametros.update');
    Route::delete('/certification/{id}/destroy', [CertificationController::class, 'destroy'])->name('certification.destroy');

    // --ROTAS DE TESTES--
    // Chega a conexão com a API do Whatsapp
    Route::get('/check-connection', function () {
        try {
            // Faz uma requisição GET para a API Evolution
            $response = Http::get('http://evolution_api:8080/');
            // Verifica se a resposta foi bem-sucedida
            if ($response->successful()) {
                $status = 'Comunicação bem-sucedida! API Evolution está respondendo.';
            } else {
                $status = 'A API Evolution não está respondendo corretamente. Status Code: ' . $response->status();
            }
        } catch (\Exception $e) {
            // Captura erros de conexão ou outras exceções
            $status = 'Erro ao tentar se comunicar com a API Evolution: ' . $e->getMessage();
        }
        // Retorna a view com o status da comunicação
        return view('check.check-connection', compact('status'));
    });

    // Testa o funcionamento do no laravel
    Route::get('/vue-test', function () {
        return view('vue.index');
    });
    // Testa a metodo get das instâncias
    Route::get('/check-get-instances', function () {
        return view('check.get-instances');
    });
    // Teste unitários
    Route::get('/testes', function () {
        return view('testes');
    })->name('testes');
    });
