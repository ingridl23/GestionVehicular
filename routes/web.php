<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\Auth\ForcedPasswordController;
Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'force.password'])->group(
    function () {

        /* Fuerzo a que actualice la contraseña la 1era vez que se loguea*/
        Route::get('/force-password', [ForcedPasswordController::class, 'edit'])
            ->middleware('auth')
            ->name('password.force');
Route::post('/viajes/{id}/gasto', [GastoController::class, 'calcular']);
Route::get('/viajes/{viaje}/gasto/preview', [GastoController::class, 'preview']);
Route::post('/viajes/{viaje}/gasto', [GastoController::class, 'calcular']);
    });
