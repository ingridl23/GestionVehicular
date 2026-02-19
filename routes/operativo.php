<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\Reservas\ReservaController;

 Route::middleware(['auth', 'role:Operativo'])
    ->prefix('operativo')
    ->name('operativo.')
    ->group(function () {

        Route::patch('/vehiculos/{vehiculo}/datos', [VehiculoController::class, 'registrarDatos'])
            ->middleware('permission:registrar_datos_vehiculos');

            //rutas nuevas
        Route::get('/vehiculos', [VehiculoController::class, 'porDependencia'])
            ->middleware('permission:ver_vehiculos_dentro_dependencia');


            Route::get('/dashboard',[UserController::class, 'dashboard2'])
            ->name('dashboard2');

        // crear reporte (usuario)
        Route::get('/reportes/crear', [ReporteController::class, 'create'])
            ->name('reportes.create');

        Route::post('/reportes', [ReporteController::class, 'store'])
            ->name('reportes.store');


        Route::get('/reportes', [ReporteController::class, 'misReportesOperativo'])
         ->name('reportes.index');


        Route::get('/mis-reportes', [ReporteController::class, 'misReportesOperativo'])
         ->name('reportes.mis');

      Route::get('/reportes/{reporte}', [ReporteController::class, 'show'])
    ->name('reportes.detalles');

Route::post(
    '/reportes/{reporte}/comentarios',
    [ReporteController::class, 'agregarComentario']
)->name('reportes.comentarios');

        //EDITAR CONDUCTOR

        Route::get('/editar-conductor', [ReservaController::class, 'formularioEditarConductor'])->name('editar-conductor');
        Route::patch('/editar-conductor/{id}', [ReservaController::class, 'editarConductor'])->name('update.conductor');

       /* Route::patch('/reportes/{reporte}/estado', [ReporteController::class, 'cambiarEstado'])
    ->name('reportes.estado');*/

       // VIAJES


});


