<?php

use App\Http\Controllers\DependenciaController;
use Illuminate\Support\Facades\Route;

    Route::middleware(['auth:sanctum'])->group(function () {
    });

    Route::post('/filtrar-dependencias', [DependenciaController::class, 'filtrarDependencias']);
    Route::post('/cambiar-estado/{id}', [DependenciaController::class, 'cambiarActivaDependencia']);