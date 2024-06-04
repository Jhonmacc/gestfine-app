<?php
use App\Http\Controllers\CertificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Route::get('/', [LoginController::class, 'welcome']);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    Route::get('/dashboard/chart-data', [CertificationController::class, 'getChartData'])->name('dashboard.chartData');
    Route::get('/certification', [CertificationController::class, 'index'])->name('certification.index');
    Route::get('/certification/download/{id}', [CertificationController::class, 'download'])->name('certification.download');
    Route::post('/certification/validate', [CertificationController::class, 'validateCertification'])->name('certification.validate');
    Route::delete('/certification/{id}/destroy', [CertificationController::class, 'destroy'])->name('certification.destroy');

//Rotas de Testes
    Route::get('/testes', function () {
        return view('testes');
    })->name('testes');
});
