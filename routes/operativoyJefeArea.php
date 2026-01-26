<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\VehiculoController;
Route::middleware(['auth', 'role:Operativo|Jefe de Area'])
    ->prefix('operativo')
    ->name('operativo.')
    ->group(function () {

        Route::patch('/vehiculos/{vehiculo}/datos', [VehiculoController::class, 'registrarDatos'])
            ->middleware('permission:registrar_datos_vehiculos');

            //rutas nuevas
            Route::get('/vehiculos', [VehiculoController::class, 'porDependencia'])
   ->middleware('permission:ver_vehiculos_dentro_dependencia');


        // Cambiar conductor en reserva activa
       Route::post('/reservas/{id}/cambiar-conductor', [ReservaController::class, 'cambiarConductor'])
      ->middleware('permission:asignar_conductor_en_reserva_activa');

    Route::patch('/reservas/{id}/finalizar', [ReservaController::class, 'finalizar'])
    ->middleware('permission:finalizar_reserva_interna');


     Route::get('/reservas/historial', [ReservaController::class, 'historial'])
      ->middleware('permission:ver_historial_reservas');


});
