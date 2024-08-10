<?php

use App\Http\Controllers\CertificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ParametroController;
use App\Http\Controllers\UserController;

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
    Route::resource('parametros', ParametroController::class)->except(['show']);
    Route::resource('users', UserController::class)->except(['create', 'edit', 'show']);

    // Rotas de Controle de Certificados
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
    Route::post('/parametros/update', [ParametroController::class, 'updateParameters'])->name('parametros.update');
    Route::post('/parametros/create', [ParametroController::class, 'store'])->name('parametros.store');

    // Rotas add números de telefone
    Route::post('/certification/update-number', [CertificationController::class, 'updateNumber']);

    Route::delete('/certification/{id}/destroy', [CertificationController::class, 'destroy'])->name('certification.destroy');

    // Rotas de Testes
    Route::get('/testes', function () {
        return view('testes');
    })->name('testes');
});
