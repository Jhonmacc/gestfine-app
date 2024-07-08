<?php
use App\Http\Controllers\CertificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ParametroController;

Route::get('/', [LoginController::class, 'welcome']);
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::resource('parametros', ParametroController::class)->except(['show']);


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard.index');

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

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

    Route::delete('/certification/{id}/destroy', [CertificationController::class, 'destroy'])->name('certification.destroy');

    // Rotas de Testes
    Route::get('/testes', function () {
        return view('testes');
    })->name('testes');
});
