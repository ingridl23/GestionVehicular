<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\Reservas\ReservaController;
use App\Http\Controllers\Reservas\BaseReservaController;
use App\Http\Controllers\ViajeController;

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

        Route::get('/editar-conductor/{id}', [ReservaController::class, 'formularioEditarConductor'])->name('editar-conductor');
        Route::patch('/editar-conductor/{id}', [ReservaController::class, 'editarConductor'])->name('update.conductor');



    //*********************************************************************************************************************** */
    /*********************************Reservas de Vehiculos Operativos ****************************************************** */
    //get verReservas
      Route::get('/mis-reservas', [ReservaController::class, 'misReservas'])->name('mis-reservas');
      Route::get('/listado-reservas/{id}', [ReservaController::class, 'show'])
    ->name('reservas.reserva');
      //ver formulario de crear reservas
       Route::get('/reservas/form', [ReservaController::class, 'mostrarFormulario'])->name('reservas-form');
        Route::post('/reserva/create', [ReservaController::class, 'crearReserva'])->name('reservar');

        //ver formulario de edicion de una reserva seleccionada
         Route::get('/reserva/update/', [ReservaController::class, 'mostrarFormularioUpdate'])->name('reserva-form-update');
         //actualizar reserva
          Route::post('/reserva/update', [BaseReservaController::class, 'editarReserva'])->name('actualizar-reserva');
       // get filtrarReservasInternas
      Route::get('/reservas/filtro', [ReservaController::class, 'filtrarReservasInternas'])->name('filtrar-reservas-int');
    /**********************************Viajes Para Operativos ****************************************************************** */
     /******************************************************************************************************************* */
     /********************************** VIAJES OPERATIVOS **********************************/

Route::get('/viajes', [ViajeController::class, 'index'])
    ->name('viajes.index');

Route::get('/viajes/{viaje}', [ViajeController::class, 'show'])
    ->name('viajes.show');

Route::post('/viajes/{reserva}/comenzar', [ViajeController::class, 'comenzarViaje'])
    ->name('viajes.comenzar');

Route::post('/viajes/{viaje}/finalizar', [ViajeController::class, 'finalizarViaje'])
    ->name('viajes.finalizar');

     });


