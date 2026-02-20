<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\AlertaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DependenciaController;
use App\Http\Controllers\Reservas\PrestamoController;
use App\Http\Controllers\Reservas\ReservaController as ReservaController;
use App\Services\CombustibleApiService;

// Ruta raíz redirige al login
Route::get('/', [HomeController::class, 'inicio']);
Route::get('/reset', [HomeController::class, 'reset'])->name('auth.passwords.reset');

// *******************  RUTAS PROTEGIDAS (requieren autenticación)   **************************
Route::middleware(['auth', 'permission:ver_todos_usuarios|ver_personal_dependencia'])->group(function () {
    Route::get('/admin/usuarios', [UserController::class, 'index'])
        ->name('admin.usuarios.index');
});

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');

    // Alertas
    Route::get('/alertas/recientes', [AlertaController::class, 'recientes']);
    Route::get('/alertas', [AlertaController::class, 'index'])->name('alertas.index');
    Route::get('/alertas/{tipo}/{id}', [AlertaController::class, 'porEntidad'])->name('alertas.porEntidad');

    // Auditoría
    Route::get('/auditoria', [HistorialController::class, 'index'])->name('auditoria.index');

    // Vehículos
    Route::get('/vehiculos', [VehiculoController::class, 'sectionVehiculo'])->name('vehiculos.index');
    Route::get('/vehiculos/{vehiculo}', [VehiculoController::class, 'detalle'])->name('vehiculos.show');
    Route::post('/vehiculos', [VehiculoController::class, 'store']);
    Route::put('/vehiculos/{vehiculo}', [VehiculoController::class, 'update'])->name('vehiculos.update');
    Route::delete('/vehiculos/{vehiculo}', [VehiculoController::class, 'destroy'])->name('vehiculos.destroy');
    Route::get('/buscar-vehiculos', [VehiculoController::class, 'index'])->name('vehiculos.buscar');

    // Personal de dependencia
    Route::get('/dependencias/personal', [UserController::class, 'usuariosPorDependencia'])
        ->middleware('permission:ver_personal_dependencia')
        ->name('personal.index');

    /*************************************  PERFIL DE USUARIO   ******************************* */

    // Ver mi perfil (usuario logueado)
    Route::get('/profile', [UserController::class, 'myProfile'])->name('profile.show');

    // Actualizar mi perfil
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');

    // Ver perfil de otro usuario (requiere permisos)
    Route::get('/profile/{usuario}', [UserController::class, 'show'])->name('profile.view');

    // Actualizar perfil de otro usuario (requiere permisos)
    Route::put('/profile/{usuario}/update', [UserController::class, 'updateProfile'])->name('profile.admin-update');

    /**************************  REPORTES  **********************/
    // Reportes
   // ver mi reporte (chat)
        Route::get('/reportes', [ReporteController::class, 'misReportes'])
            ->name('reportes.mis');


   /********************************** Rutas de dependencia ************************************************/

    Route::prefix('dependencia')->name('dependencia.')->group(function () {

        // Préstamos
        Route::get('/prestamos', [ReservaController::class, 'prestamos'])
            ->name('prestamos.index')
            ->middleware('permission:ver_prestamos');

        // Usuarios de la dependencia
        Route::get('/usuarios', [UserController::class, 'usuariosPorDependencia'])
            ->middleware('permission:ver_usuarios_dependencia')
            ->name('usuarios');
    });

    /*************************************  RESERVAS GENERAL   ******************************* */

    //Aca se declararon las rutas que puede acceder todos los usuarios (independientemente del rol)
    //Las especificas por rol se declararon en AdminGeneral.php

    Route::get('/listado-reservas', [ReservaController::class, 'verReservas'])->middleware('permission:ver_reservas_internas')->name('reservas.internas');
    Route::get('/listado-prestamos', [PrestamoController::class, 'verReservas'])->middleware('permission:ver_reservas_prestamos')->name('reservas.prestamos');

    Route::get('/listado-reservas/{id}', [ReservaController::class, 'verReserva'])->name('reservas.reserva'); //Vista individual

    //FILTROS
    Route::post('/filtrar-reservas-internas', [ReservaController::class, 'filtrarReservasInternas'])->middleware('web');
    Route::post('/filtrar-reservas-externas', [PrestamoController::class, 'filtrarReservasExternas'])->middleware('web');

});




// RUTA DE PRUEBA API
Route::get('/test-combustible', function (CombustibleApiService $service) {
    return response()->json([
        'resultado' => $service->obtenerPrecioActual()
    ]);
})->name('test.combustible');
