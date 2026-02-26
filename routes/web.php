<?php

use App\Http\Controllers\Api\GeoController;
use App\Http\Controllers\GabineteController;
use App\Http\Controllers\OrcamentoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequerenteController;
use App\Http\Controllers\ServicoController;
use App\Http\Controllers\SubcontratadoController;
use App\Http\Controllers\TipoImovelController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('requerentes', RequerenteController::class);
    Route::resource('gabinetes', GabineteController::class);
    Route::resource('subcontratados', SubcontratadoController::class);
    Route::resource('tipo-imoveis', TipoImovelController::class)->parameters(['tipo-imoveis' => 'tipoImovel']);
    Route::resource('servicos', ServicoController::class);
    Route::resource('orcamentos', OrcamentoController::class);

    Route::get('api/distritos/{distrito}/concelhos', [GeoController::class, 'concelhos'])->name('api.distritos.concelhos');
    Route::get('api/concelhos/{concelho}/freguesias', [GeoController::class, 'freguesias'])->name('api.concelhos.freguesias');
});

require __DIR__.'/auth.php';
