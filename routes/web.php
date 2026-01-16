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



//*******************para admin general**************************
Route::middleware(['auth'])->group(function () {


    // DASHBOARD
    Route::get('/dashboard', [HomeController::class, 'dashboard'])
    ->name('dashboard');


    // Listado de vehículos
    Route::get('/listado-vehiculos', [VehiculoController::class, 'index'])->name('vehiculos.index');
    Route::get('/vehiculos', [VehiculoController::class, 'sectionVehiculo']);
    Route::get('/vehiculos/{vehiculo}', [VehiculoController::class, 'show']);

    // VEHÍCULOS (CRUD con permisos)
    Route::post('/vehiculos', [VehiculoController::class, 'store'])
        ->middleware('permission:cargar_vehiculo')
        ->name('vehiculos.store');

    Route::put('/vehiculos/{vehiculo}', [VehiculoController::class, 'update'])
        ->middleware('permission:editar_vehiculo')
        ->name('vehiculos.update');

    Route::patch('/vehiculos/{vehiculo}/asignacion', [VehiculoController::class, 'updateAsignacion'])
        ->middleware('permission:modificar_asignacion_vehiculo')
        ->name('vehiculos.asignacion.update');

    Route::delete('/vehiculos/{vehiculo}', [VehiculoController::class, 'destroy'])
        ->middleware('permission:eliminar_vehiculo')
        ->name('vehiculos.destroy');

        //RESERVAS CON PERMISOS
         Route::get('/listado-reservas', [ReservaController::class, 'reservas'])->name('reservas.internas');
                  Route::get('/listado-prestamos', [ReservaController::class, 'prestamos'])->name('reservas.prestamos');
    // VIAJES Y GASTOS
    Route::post('/viajes/{id}/gasto', [GastoController::class, 'calcular'])
        ->name('viajes.gasto.calcular');
    Route::get('/viajes/{viaje}/gasto/preview', [GastoController::class, 'preview'])
        ->name('viajes.gasto.preview');


    // ALERTAS
    Route::get('/alertas', [AlertaController::class, 'index'])
        ->name('alertas.index');
    Route::get('/alertas/{tipo}/{id}', [AlertaController::class, 'porEntidad'])
        ->name('alertas.porEntidad');
    Route::patch('/alertas/{id}/resolver', [AlertaController::class, 'resolver'])
        ->name('alertas.resolver');

    // REPORTES
    Route::get('/reportes', [ReporteController::class, 'index'])
        ->middleware('permission:ver_reportes_dependencia|ver_reportes_general')
        ->name('reportes.index');
    Route::post('/reportes', [ReporteController::class, 'store'])
        ->name('reportes.store');
    Route::get('/reportes/{reporte}', [ReporteController::class, 'show'])
        ->name('reportes.show');
    Route::patch('/reportes/{reporte}/estado', [ReporteController::class, 'cambiarEstado'])
        ->name('reportes.cambiarEstado');
});


//crear grupo de rutas para operativo
// Ruta principal para operador
Route::get('/operador', [UserController::class, 'operador']);






//para jefe de area

//para dueño de dependencia





// RUTA DE PRUEBA API
Route::get('/test-combustible', function (CombustibleApiService $service) {
    return response()->json([
        'resultado' => $service->obtenerPrecioActual()
    ]);
})->name('test.combustible');
