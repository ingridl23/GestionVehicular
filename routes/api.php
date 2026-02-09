<?php

use App\Http\Controllers\DependenciaController;
use App\Http\Controllers\VehiculoController;
use Illuminate\Support\Facades\Route;



    Route::middleware(['auth'])->group(function () {
    });

    Route::post('/filtrar-dependencias', [DependenciaController::class, 'filtrarDependencias']);
    Route::post('/cambiar-estado/{id}', [DependenciaController::class, 'cambiarActivaDependencia']);





    // Rutas API para vehículos

    Route::middleware('auth')->group(function () {
        Route::get('/vehiculos', [VehiculoController::class, 'index']);
    });
