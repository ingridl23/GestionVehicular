<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\VehiculoController;
Route::middleware(['auth', 'role:Administrador de Dependencia|Jefe de Area'])
    ->prefix('dependencia')
    ->name('dependencia.')
    ->group(function () {

        //Route::get('/reportes', [ReporteController::class, 'index'])->middleware('permission:ver_reportes_dependencia');
        //tambien los responde y actualiza a los reportes
        Route::post('/reportes/{id}/actualizar', [ReporteController::class, 'update'])->middleware('permission:actualizar_reportes');

        Route::get('/reservas', [ReservaController::class, 'reservas'])->middleware('permission:ver_reservas_internas')->name('reservas.index');

        Route::post('/reservas/solicitar', [ReservaController::class, 'store'])
                    ->middleware('permission:solicitar_reserva_interna');

        Route::post('/reservas/{id}/autorizar', [ReservaController::class, 'autorizar'])
            ->middleware('permission:autorizar_reservas_internas');

        Route::post('/reservas/{id}/rechazar', [ReservaController::class, 'rechazar'])
       ->middleware('permission:rechazar_reservas_internas');


     // Vehículos de su dependencia
        Route::get('/vehiculos', [VehiculoController::class, 'porDependencia'])
         ->middleware('permission:ver_vehiculos_dentro_dependencia');

});




