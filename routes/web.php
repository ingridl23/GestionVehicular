<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\AlertaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DependenciaController;
use App\Http\Controllers\Auth\ForcedPasswordController;
use App\Services\CombustibleApiService;

// Fortify ya maneja estas rutas automáticamente:
// POST /login
// POST /logout
// GET /login (si 'views' => true en fortify.php)

// Ruta raíz redirige al login
Route::get('/', [HomeController::class, 'inicio']);
Route::get('/reset', [HomeController::class, 'reset'])->name('auth.passwords.reset');


// RUTAS PROTEGIDAS (requieren autenticación)



// *******************  para todos los usuarios   **************************
 Route::middleware(['auth'])->group(function () {
    Route::get('/alertas/recientes', [AlertaController::class, 'recientes'])
    ->middleware('auth');

    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');

    Route::get('/auditoria', [HistorialController::class, 'index'])
        ->name('auditoria.index');

 Route::get('/vehiculos', [VehiculoController::class, 'sectionVehiculo'])
        ->name('vehiculos.index');

        Route::get('/vehiculos/{vehiculo}', [VehiculoController::class, 'detalle'])
    ->name('vehiculos.show');
 Route::post('/vehiculos', [VehiculoController::class, 'store']);
    Route::put('/vehiculos/{vehiculo}', [VehiculoController::class, 'update'])->name('vehiculos.update');
    Route::delete('/vehiculos/{vehiculo}', [VehiculoController::class, 'destroy'])->name('vehiculos.destroy');

    Route::get('/buscar-vehiculos', [VehiculoController::class, 'index'])->name('vehiculos.buscar');


        /************************************************************** */
    Route::get('/dependencias/personal', [UserController::class, 'usuariosPorDependencia'])->middleware('permission:ver_personal_dependencia')->name('personal.index');

    Route::get('/alertas', [AlertaController::class, 'index'])->name('alertas.index');
    Route::get('/alertas/{tipo}/{id}', [AlertaController::class, 'porEntidad'])->name('alertas.porEntidad');

    /**************************para reportes ********************* */

    // mensajes
    Route::post('/reportes/{reporte}/comentarios', [ReporteController::class, 'agregarComentario']);

    // cambiar estado (admin)
    Route::patch('/admin/reportes/{reporte}/estado', [ReporteController::class, 'cambiarEstado']);
    Route::middleware(['auth'])
    ->prefix('dependencia')
    ->name('dependencia.')
    ->group(function () {

        // Préstamos (usa ReservaController)
        Route::get('/prestamos', [ReservaController::class, 'prestamos'])
            ->name('prestamos.index')
            ->middleware('permission:ver_prestamos');

              Route::get('/reportes/{reporte}', [ReporteController::class, 'show'])
        ->name('reportes.show');
        Route::resource('reportes', ReporteController::class)
            ->only(['index'])
          ->middleware('permission:ver_reportes_dependencia|ver_reportes_general|ver_reportes_operativos');

          Route::get('/reservas', [ReservaController::class, 'reservas'])
              ->middleware('permission:ver_reservas')
              ->name('reservas.index');
    });


});







// RUTA DE PRUEBA API
Route::get('/test-combustible', function (CombustibleApiService $service) {
    return response()->json([
        'resultado' => $service->obtenerPrecioActual()
    ]);
})->name('test.combustible');
