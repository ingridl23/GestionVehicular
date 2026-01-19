<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\VehiculoController;

use App\Http\Controllers\ReporteController;

use App\Http\Controllers\UserController;

use App\Http\Controllers\DependenciaController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\ReservaController;

Route::middleware(['auth', 'role:Administrador General'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::resource('/usuarios', UserController::class);

        Route::resource('/vehiculos', VehiculoController::class)->only(['store','update','destroy']);

        Route::resource('/reservas', ReservaController::class);

        Route::post('/reservas/{id}/rechazar', [ReservaController::class, 'rechazar'])
       ->middleware('permission:rechazar_reservas_global');

       Route::get('/auditoria', [HistorialController::class,'index'])
       ->middleware('permission:ver_auditoria');


      // Reportes globales
       Route::resource('/reportes', ReporteController::class);


       //sub prefijo para permisos y eventos de dependencias
       //abarca para dependencias hijas tambien
       Route::prefix('dependencias')->name('dependencias.')->group(function () {
       Route::get('/', [DependenciaController::class, 'verDependencias'])
        ->middleware('permission:ver_dependencias')
        ->name('index');

     Route::get('/filtrar', [DependenciaController::class, 'filtrarDependencias'])
        ->middleware('permission:ver_dependencias')
        ->name('filtrar');

     Route::get('/crear', [DependenciaController::class, 'datosParaCrearDependencia'])
        ->middleware('permission:crear_dependencias')
        ->name('create');

    Route::post('/', [DependenciaController::class, 'crearDependencia'])
        ->middleware('permission:crear_dependencias')
        ->name('store');

    Route::get('/{id}', [DependenciaController::class, 'verDependencia'])
        ->middleware('permission:ver_dependencias')
        ->name('show');

    Route::get('/{id}/editar', [DependenciaController::class, 'datosParaEditarDependencia'])
        ->middleware('permission:editar_dependencias')
        ->name('edit');

    Route::put('/{id}', [DependenciaController::class, 'editarDependencia'])
        ->middleware('permission:editar_dependencias')
        ->name('update');

    Route::delete('/{id}', [DependenciaController::class, 'eliminarDependencia'])
        ->middleware('permission:eliminar_dependencias')
        ->name('destroy');

    Route::patch('/{id}/activa', [DependenciaController::class, 'cambiarActivaDependencia'])
        ->middleware('permission:editar_dependencias')
        ->name('toggle');
});


});









