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
   // Route::get('/vehiculos', [VehiculoController::class, 'sectionVehiculo'])->middleware('permission:ver_vehiculos');
    Route::get('/vehiculos', [VehiculoController::class, 'sectionVehiculo'])
    ->middleware('permission:ver_vehiculos|ver_vehiculos_dentro_dependencia')->name('vehiculos.index');

    Route::get('/vehiculos/{vehiculo}', [VehiculoController::class, 'show'])
        ->middleware('permission:ver_vehiculos');

    Route::get('/dependencias/personal', [UserController::class, 'usuariosPorDependencia'])->middleware('permission:ver_personal_dependencia')->name('personal.index');

    Route::get('/alertas', [AlertaController::class, 'index'])->name('alertas.index');
    Route::get('/alertas/{tipo}/{id}', [AlertaController::class, 'porEntidad'])->name('alertas.porEntidad');

    Route::middleware(['auth'])
    ->prefix('dependencia')
    ->name('dependencia.')
    ->group(function () {

        // Préstamos (usa ReservaController)
        Route::get('/prestamos', [ReservaController::class, 'prestamos'])
            ->name('prestamos.index')
            ->middleware('permission:ver_prestamos');
    });


 Route::middleware(['auth'])
    ->prefix('dependencia')
    ->name('dependencia.')
    ->group(function () {

        Route::resource('reportes', ReporteController::class)
            ->only(['index', 'show'])
            ->middleware('permission:ver_reportes_dependencia');
    });


});


Route::middleware(['auth', 'role:Operativo'])
    ->prefix('operativo')
    ->name('operativo.')
    ->group(function () {

        Route::get('/dashboard',[UserController::class, 'dashboard2'])
        ->name('dashboard');

        Route::get('/reportes', [ReporteController::class, 'index'])
            ->middleware('permission:ver_reportes_dependencia')
            ->name('reportes.index');

        Route::get('/reservas', [ReservaController::class, 'reservas'])
            ->middleware('permission:ver_reservas')
            ->name('reservas.index');
});




// RUTA DE PRUEBA API
Route::get('/test-combustible', function (CombustibleApiService $service) {
    return response()->json([
        'resultado' => $service->obtenerPrecioActual()
    ]);
})->name('test.combustible');
