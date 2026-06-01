<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DependenciaController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\Reservas\PrestamoController;
use App\Http\Controllers\Reservas\ReservaController;

Route::middleware(['auth', 'role:Administrador General|Administrador de Dependencia'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/dashboard', [UserController::class, 'adminDashboard'])
        ->name('admin.dashboard');

        Route::resource('/usuarios', UserController::class);

        Route::resource('/vehiculos', VehiculoController::class)->only(['store','update','destroy']);


       Route::get('/auditoria', [HistorialController::class,'index'])
       ->middleware('permission:ver_auditoria')->name('auditoria.index');




       // Reportes globales
      Route::resource('reportes', ReporteController::class)
            ->middleware('permission:ver_reportes_dependencia');


    // Cambiar estado (admin)
    Route::patch('/reportes/{reporte}/estado', [ReporteController::class, 'cambiarEstado']);

    //ruta para habilitar eliminar un reporte ya atendido y cerrado.
   Route::delete('/reportes/{reporteId}/eliminar', [ReporteController::class, 'deleteReporte'])->name('reporte.eliminar');

   // Mensajes en reportes
        Route::post('/reportes/{reporte}/comentarios',
            [ReporteController::class, 'agregarComentario']
        )->name('reportes.comentarios');

});



       Route::middleware(['auth', 'role:Administrador General|Administrador de Dependencia|Jefe de Area'])
       ->prefix('dependencias')->name('dependencias.')->group(function () {

            Route::post('/filtrar', [DependenciaController::class, 'filtrarDependencias'])->middleware('permission:ver_dependencias')->name('filtrar');

            Route::get('/crear', [DependenciaController::class, 'datosParaCrearDependencia'])->middleware('permission:crear_dependencias')->name('create');

            Route::get('/', [DependenciaController::class, 'verDependencias'])->middleware('permission:ver_dependencias')->name('index');

            Route::delete('/{id}', [DependenciaController::class, 'eliminarDependencia'])->middleware('permission:eliminar_dependencias')->name('destroy');

            Route::get('/{id}', [DependenciaController::class, 'verDependencia'])->middleware('permission:ver_dependencias')->name('show');

            Route::post('/', [DependenciaController::class, 'crearDependencia'])->middleware('permission:crear_dependencias')->name('store');

            Route::get('/{id}/editar', [DependenciaController::class, 'datosParaEditarDependencia'])->middleware('permission:editar_dependencias')->name('edit');

            Route::patch('/{id}', [DependenciaController::class, 'editarDependencia'])->middleware('permission:editar_dependencias')->name('update');

            Route::patch('/{id}/activa', [DependenciaController::class, 'cambiarActivaDependencia'])->middleware('permission:editar_dependencias')->name('toggle');

        });


/******************************************************* */

Route::middleware(['auth', 'role:Administrador General|Administrador de Dependencia|Jefe de Area'])
    ->prefix('admin')->name('admin.')
    ->group(function () {
            //MOSTRAR FORMULARIO DE INTERNA Y PRESTAMO
            Route::get('/agregar-reserva', [ReservaController::class, 'mostrarFormulario'])->name('reservas.form.agregar')->middleware('permission:solicitar_reserva_interna');
            Route::get('/agregar-prestamo', [PrestamoController::class, 'mostrarFormulario'])->name('prestamo.form.agregar')->middleware('permission:solicitar_prestamo');

            //METODO PARA CREAR LA RESERVA INTERNA Y PRESTAMO
            Route::post('/agregar-reserva', [ReservaController::class, 'crearReserva'])->name('reservas.internas.crear')->middleware('permission:solicitar_reserva_interna');
            Route::post('/agregar-prestamo', [PrestamoController::class, 'crearReserva'])->name('reservas.externas.crear')->middleware('permission:solicitar_prestamo');

            // CANCELAR RESERVA
            Route::patch('/cancelar-reserva/{id}', [ReservaController::class, 'cancelarReserva'])->name('reservas.cancelar')
             ->middleware('permission:cancelar_reserva_interna|cancelar_prestamo');


 Route::patch('/aprobar-reserva/{id}', [ReservaController::class, 'autorizarReserva'])
    ->name('reservas.aprobar')
    ->middleware('permission:autorizar_reservas_internas');
             });



Route::middleware(['auth', 'role:Administrador General|Administrador de Dependencia'])
    ->prefix('admin')->name('admin.')
    ->group(function () {

        //EDITAR

        //METODO PATCH
        Route::patch('/editar-reserva/{id}', [ReservaController::class, 'editarReserva'])->middleware('permission:actualizar_reserva_interna')->name('reservas.internas.editar');
        Route::patch('/editar-prestamo/{id}', [PrestamoController::class, 'editarReserva'])->middleware('permission:actualizar_prestamo')->name('reservas.externas.editar');

        //MOSTRAR FORMULARIOS
        Route::get('/editar-reserva/{id}', [ReservaController::class, 'mostrarFormularioUpdate'])->name('reservas.form.editar')->middleware('permission:actualizar_reserva_interna');
        Route::get('/editar-prestamo/{id}', [PrestamoController::class, 'mostrarFormularioUpdate'])->name('prestamo.form.editar')->middleware('permission:actualizar_prestamo');


        //------------------------------------
        // AUTORIZAR
        Route::get('/autorizar-prestamos', [PrestamoController::class, 'verReservasPendientes'])->name('reservas.autorizar-prestamos')->middleware('permission:ver_solicitudes_prestamos');
        Route::patch('/autorizar-prestamo/{id}', [PrestamoController::class, 'autorizarPrestamo'])->name('reservas.autorizar')->middleware('permission:autorizar_prestamos');
        Route::patch('/rechazar-prestamo/{id}', [PrestamoController::class, 'rechazarPrestamo'])->name('reservas.rechazar')->middleware('permission:rechazar_prestamos');
        Route::post('/filtrar-reservas-externas-autorizar', [PrestamoController::class, 'filtrarAutorizarPrestamos'])->name('reservas.autorizar.filtrar')->middleware('permission:ver_solicitudes_prestamos');
    });












