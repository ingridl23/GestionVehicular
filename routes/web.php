<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\AlertaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ViajeController;
use App\Http\Controllers\HomeController;
use App\Models\Viaje;
use App\Http\Controllers\Reservas\PrestamoController;
use App\Http\Controllers\Reservas\ReservaController;
//use App\Http\Controllers\Auth\PasswordResetLinkController;
//use App\Services\CombustibleApiService;
// Ruta raíz redirige al login
    Route::get('/', [HomeController::class, 'inicio']);

//Route::get('/reset-password',[PasswordResetLinkController::class, 'create'])->name('password.reset');

// *******************  RUTAS PROTEGIDAS (requieren autenticación)   **************************
    Route::middleware(['auth', 'permission:ver_todos_usuarios|ver_personal_dependencia'])->group(function () {
    Route::get('/admin/usuarios', [UserController::class, 'index'])
        ->name('admin.usuarios.index');
    });


    Route::middleware(['auth'])->group(function () {

     // Dashboard
    //  Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');

    // Alertas
    Route::get('/alertas/recientes', [AlertaController::class, 'recientes']);
    Route::get('/alertas', [AlertaController::class, 'index'])->name('alertas.index');
    Route::get('/alertas/{tipo}/{id}', [AlertaController::class, 'porEntidad'])->name('alertas.porEntidad');

    // Auditoría y dashboard
    Route::get('/auditoria', [UserController::class, 'dashboard'])->name('auditoria.index');


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
    Route::post('/profile/photo', [UserController::class, 'updatePhoto'])
    ->name('profile.updatePhoto');
    // Ver perfil de otro usuario (requiere permisos)
    Route::get('/profile/{usuario}', [UserController::class, 'show'])->name('profile.view');

    // Actualizar perfil de otro usuario (requiere permisos)
    Route::put('/profile/{usuario}/update', [UserController::class, 'updateProfile'])->name('profile.admin-update');

    /**************************  REPORTES  **********************/
    // Reportes
   // ver mi reporte (chat)
        Route::get('/reportes', [ReporteController::class, 'misReportes'])
            ->name('reportes.mis');

 /********************************VIAJES ******************************************** */
 Route::post('/viajes/iniciar/{reserva}', [ViajeController::class, 'iniciar'])
   ->name('viajes.iniciar');
   Route::post('/viajes/{viaje}/finalizar', [ViajeController::class, 'finalizarViaje'])
    ->name('viajes.finalizaradmin');


    Route::get('/viajes', [ViajeController::class, 'index'])
    ->name('viajes.index');

    Route::get('/viajes/{viaje}', [ViajeController::class, 'show'])
    ->name('viajes.show');

    Route::get('/viajes/{viaje}/mapa', [ViajeController::class, 'mapaVirtual'])
    ->name('viajes.mapa');

    Route::post('/viajes/{reserva}/comenzar', [ViajeController::class, 'comenzarViaje'])
    ->name('viajes.comenzar');
    Route::post('/viajes/finalizar/{viaje}', [ViajeController::class, 'finalizar'])
    ->name('viajes.finalizar');

    Route::get('/api/viajes/{viaje}/coordenadas', function (Viaje $viaje) {
    return response()->json(
        $viaje->coordenadas()
              ->latest('fecha_hora')
              ->limit(20)
              ->get()
    );
});


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

        Route::post('/usuarios', [UserController::class, 'store'])
            ->middleware('permission:crear_usuario')
            ->name('usuarios.store');

        Route::put('/usuarios/{usuario}', [UserController::class, 'update'])
        ->name('usuarios.update');

        Route::delete('/usuarios/{usuario}', [UserController::class, 'destroy'])
        ->name('usuarios.destroy');
       });



        Route::middleware(['auth', 'role:Administrador de Dependencia'])
         ->prefix('dependencia')
         ->name('dependencia.')
         ->group(function () {

        Route::get('/reportes', [ReporteController::class, 'index'])
            ->middleware('permission:ver_reportes_dependencia')
            ->name('reportes.index');
       });


   /*********************************************** ALERTAS ****************************************** */

       Route::post('/alertas/resolver-multiples', [AlertaController::class, 'resolverMultiples']);
       Route::post('/alertas/resolver-todas', [AlertaController::class, 'resolverTodas']);
       Route::get('/alertas/{id}', [AlertaController::class, 'show']);
       Route::post('/alertas/{id}/resolver', [AlertaController::class, 'resolver']);


    /*********************************************************************************************/
    /***************************  NOTIFICACIONES *************************************************/
    /*********************************************************************************************/


       Route::post('/notificaciones/{id}/leer', function ($id) {
         $notificacion = auth()->user()
        ->unreadNotifications()
        ->where('id', $id)
        ->firstOrFail();

        $notificacion->markAsRead();
         return response()->json(['success' => true]);
        })->middleware('auth')->name('notificaciones.leer');

    /*************************************  RESERVAS GENERAL   **********************************/

    //Aca se declararon las rutas que puede acceder todos los usuarios (independientemente del rol)
    //Las especificas por rol se declararon en AdminGeneral.php / operativo.php / adminDependencia.php

    Route::get('/listado-reservas', [ReservaController::class, 'verReservas'])->middleware('permission:ver_reservas_internas')->name('reservas.internas');
    Route::get('/listado-prestamos', [PrestamoController::class, 'verReservas'])->middleware('permission:ver_reservas_prestamos')->name('reservas.prestamos');

    Route::get('/listado-reservas/{id}', [ReservaController::class, 'show'])->name('reservas.reserva'); //Vista individual

    //FILTROS
    Route::post('/filtrar-reservas-internas', [ReservaController::class, 'filtrarReservasInternas'])->middleware('web');
    Route::post('/filtrar-reservas-externas', [PrestamoController::class, 'filtrarReservasExternas'])->middleware('web');

    Route::post('/filtrar-prestamos-internos', [PrestamoController::class, 'verPrestamosInternos'])->name('filtrar.prestamos.internos');

    Route::post('/filtrar-prestamos-externos', [PrestamoController::class, 'verPrestamosExternos'])->name('filtrar.prestamos.externos');


    /******************* rutas para exportaciones de excel **************************** */
        Route::get('/export/vehiculos', [VehiculoController::class, 'export'])
        ->name('vehiculos.export');

    Route::get('/export/reservas', [ReservaController::class, 'exportarReservas'])
        ->name('reservas.export');

    Route::get('/export/viajes', [ViajeController::class, 'export'])
        ->name('viajes.export');

    Route::get('/export/conductores', [UserController::class, 'exportConductores'])
        ->name('conductores.export');

    Route::get('/export/usuarios', [UserController::class, 'export'])
        ->name('usuarios.export');

    Route::get('/export/gastos', [GastoController::class, 'export'])
        ->name('gastos.export');

    Route::get('/export/reportes', [ReporteController::class, 'export'])
        ->name('reportes.export');

});

/*
// RUTA DE PRUEBA API
Route::get('/test-combustible', function (CombustibleApiService $service) {
    return response()->json([
        'resultado' => $service->obtenerPrecioActual()
    ]);
})->name('test.combustible');
*/

