<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DependenciaController;
use App\Http\Controllers\HistorialController;

Route::middleware(['auth', 'role:Administrador General'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    
        Route::resource('/usuarios', UserController::class)->middleware('permission:ver_todos_usuarios');

        Route::resource('/vehiculos', VehiculoController::class)->only(['store','update','destroy']);

    //     Route::resource('/reservas', ReservaController::class)->middleware('permission:ver_reservas_internas')  ->names([
    //     'index' => 'reservas.index',
    //     'store' => 'reservas.store',
    //     'show' => 'reservas.show',
    //     'update' => 'reservas.update',
    //     'destroy' => 'reservas.destroy',
    // ]);

    //     Route::post('/reservas/{id}/rechazar', [ReservaController::class, 'rechazar'])
    //    ->middleware('permission:rechazar_reservas_global');

       Route::get('/auditoria', [HistorialController::class,'index'])
       ->middleware('permission:ver_auditoria')->name('auditoria.index');

      

      Route::resource('reportes', ReporteController::class)
            ->only(['index', 'show', 'update'])
            ->middleware('permission:ver_reportes_dependencia');
       // Reportes globales
      Route::resource('reportes', ReporteController::class)
            ->middleware('permission:ver_reportes_dependencia');

        Route::patch(
            'reportes/{reporte}/estado',
            [ReporteController::class, 'cambiarEstado']
        )->name('reportes.estado');

        Route::post(
            'reportes/{reporte}/comentarios',
            [ReporteController::class, 'agregarComentario']
        )->name('reportes.comentarios');

       //sub prefijo para permisos y eventos de dependencias
       //abarca para dependencias hijas tambien
       Route::prefix('dependencias')->name('dependencias.')->group(function () {

            Route::get('/crear', [DependenciaController::class, 'datosParaCrearDependencia'])->middleware('permission:crear_dependencias')->name('create');

            Route::post('/', [DependenciaController::class, 'crearDependencia'])->middleware('permission:crear_dependencias')->name('store');

            Route::get('/{id}/editar', [DependenciaController::class, 'datosParaEditarDependencia'])->middleware('permission:editar_dependencias')->name('edit');

            Route::patch('/{id}', [DependenciaController::class, 'editarDependencia'])->middleware('permission:editar_dependencias')->name('update');

            Route::delete('/{id}', [DependenciaController::class, 'eliminarDependencia'])->middleware('permission:eliminar_dependencias')->name('destroy');

            Route::patch('/{id}/activa', [DependenciaController::class, 'cambiarActivaDependencia'])->middleware('permission:editar_dependencias')->name('toggle');

            //DEPENDENCIAS

            // //Editar dependencias
            // Route::get('dependencias/editar/{id}', [DependenciaController::class, 'datosParaEditarDependencia']); //formulario editar
            // Route::patch('/dependencias/{id}', [DependenciaController::class, 'editarDependencia']);

            // //crear dependencias
            // Route::get('/dependencias/crear', [DependenciaController::class, 'datosParaCrearDependencia']); //formulario crear
            // Route::post('/dependencias/crear-dependencia', [DependenciaController::class, 'crearDependencia']);

            // // Ver dependencias
            // Route::get('/dependencias', [DependenciaController::class, 'verDependencias'])->name('dependencias.index');

            // Route::get('/dependencias/{id}', [DependenciaController::class, 'verDependencia']);

            // // Eliminar dependencias
            // Route::delete('/dependencias/{id}', [DependenciaController::class, 'eliminarDependencia']);
    });


});









