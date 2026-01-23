<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\UserController;





 Route::middleware(['auth', 'role:Operativo|Jefe de Area'])
    ->prefix('operativo')
    ->name('operativo.')
    ->group(function () {

        Route::get('/reservas', [ReservaController::class, 'index'])
            ->middleware('permission:ver_reservas_internas');

        Route::post('/reservas/solicitar', [ReservaController::class, 'store'])
            ->middleware('permission:solicitar_reserva_interna');

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


        Route::get('/reservas', [ReservaController::class, 'reservas'])
            ->name('reservas.index')
            ->middleware('permission:ver_operativo_reservas');


});
