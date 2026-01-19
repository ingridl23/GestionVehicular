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



// RUTAS PROTEGIDAS (requieren autenticación)



//*******************para todos los usuarios   **************************
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');

   // Route::get('/vehiculos', [VehiculoController::class, 'sectionVehiculo'])->middleware('permission:ver_vehiculos');
    Route::get('/vehiculos', [VehiculoController::class, 'sectionVehiculo'])
    ->middleware('permission:ver_vehiculos|ver_vehiculos_dentro_dependencia');

    Route::get('/vehiculos/{vehiculo}', [VehiculoController::class, 'show'])
        ->middleware('permission:ver_vehiculos');
   // Route::get('/listado-vehiculos', [VehiculoController::class, 'index'])->name('vehiculos.index');

    Route::get('/alertas', [AlertaController::class, 'index'])->name('alertas.index');
    Route::get('/alertas/{tipo}/{id}', [AlertaController::class, 'porEntidad'])->name('alertas.porEntidad');
});






// RUTA DE PRUEBA API
Route::get('/test-combustible', function (CombustibleApiService $service) {
    return response()->json([
        'resultado' => $service->obtenerPrecioActual()
    ]);
})->name('test.combustible');
