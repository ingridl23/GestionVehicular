<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\AlertaController;
use App\Services\CombustibleApiService;
use App\Http\Controllers\Auth\ForcedPasswordController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'force.password'])->group(
    function () {

        /* Fuerzo a que actualice la contraseña la 1era vez que se loguea*/
        Route::get('/force-password', [ForcedPasswordController::class, 'edit'])
            ->middleware('auth')
            ->name('password.force');
Route::post('/viajes/{id}/gasto', [GastoController::class, 'calcular']);
Route::get('/viajes/{viaje}/gasto/preview', [GastoController::class, 'preview']);
Route::post('/viajes/{viaje}/gasto', [GastoController::class, 'calcular']);

        // CU 2 – Listado de vehículos (con filtros opcionales)
        Route::get('/vehiculos', [VehiculoController::class, 'index']);

        // CU 2 – Detalle de un vehículo
        Route::get('/vehiculos/{vehiculo}', [VehiculoController::class, 'show']);

        // CU 5 – Agregar vehículo
        Route::post('/vehiculos', [VehiculoController::class, 'store']);

        // CU 4 – Modificar vehículo
        Route::put('/vehiculos/{vehiculo}', [VehiculoController::class, 'update']);

        // CU 17 – Modificar asignación de vehículo
        Route::patch('/vehiculos/{vehiculo}/asignacion', [VehiculoController::class, 'updateAsignacion']);

        // CU 3 – Eliminar vehículo
        Route::delete('/vehiculos/{vehiculo}', [VehiculoController::class, 'destroy']);



        // Listar todas las alertas activas
        Route::get('/alertas', [AlertaController::class, 'index']);

        // Listar alertas activas por entidad
        // Ej: /alertas/vehiculo/3
        Route::get('/alertas/{tipo}/{id}', [AlertaController::class, 'porEntidad']);

        // Resolver manualmente una alerta
        Route::patch('/alertas/{id}/resolver', [AlertaController::class, 'resolver']);
    });

//ruta para testear si se conecta a la api

Route::get('/test-combustible', function (App\Services\CombustibleApiService $service) {
    return response()->json([
        'resultado' => $service->obtenerPrecioActual()
    ]);
});



