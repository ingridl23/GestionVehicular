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



    //RESERVAS

    Route::get('/agregar-reserva', [ReservaController::class, 'mostrarFormulario'])->name('reservas.form.agregar')->middleware('permission:solicitar_reserva_interna'); //FORMULARIO
    Route::get('/agregar-prestamo', [PrestamoController::class, 'mostrarFormulario'])->name('prestamo.form.agregar')->middleware('permission:solicitar_prestamo'); //FORMULARIO

    // Se deja listado de reservas en web ya que es algo que, sin importar el rol, debe verse (ya que datos se muestran, eso se filtra en el back)
    Route::get('/listado-reservas', [ReservaController::class, 'verReservas'])->middleware('permission:ver_reservas_internas')->name('reservas.internas');
    Route::get('/listado-prestamos', [PrestamoController::class, 'verReservas'])->name('reservas.prestamos');
    Route::get('/listado-reservas/{id}', [ReservaController::class, 'verReserva'])->name('reservas.reserva'); //Vista individual

    Route::patch('/cancelar-reserva/{id}', [ReservaController::class, 'cancelarReserva'])->name('reservas.cancelar');

    //EDITAR
    
    //METODO PATCH
    Route::patch('/editar-reserva/{id}', [ReservaController::class, 'editarReserva'])->middleware('permission:actualizar_reserva_interna')->name('reservas.internas.editar');
    Route::patch('/editar-prestamo/{id}', [PrestamoController::class, 'editarReserva'])->middleware('permission:actualizar_prestamo')->name('reservas.externas.editar');

    //MOSTRAR FORMULARIOS
    Route::get('/editar-reserva/{id}', [ReservaController::class, 'mostrarFormularioUpdate'])->name('reservas.form.editar')->middleware('permission:actualizar_reserva_interna'); 
    Route::get('/editar-prestamo/{id}', [PrestamoController::class, 'mostrarFormularioUpdate'])->name('prestamo.form.editar')->middleware('permission:actualizar_prestamo'); 


    //------------------------------------

    Route::post('/filtrar-reservas-internas', [ReservaController::class, 'filtrarReservasInternas'])->middleware('web');
    Route::post('/filtrar-reservas-externas', [PrestamoController::class, 'filtrarReservasExternas'])->middleware('web');
    Route::get('/autorizar-prestamos', [ReservaController::class, 'verReservasExternas'])->name('reservas.autorizar-prestamos');





    Route::post('/agregar-reserva', [ReservaController::class, 'crearReserva'])->name('reservas.internas.crear');
    Route::post('/agregar-prestamo', [PrestamoController::class, 'crearReserva'])->name('reservas.externas.crear');


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
          ->middleware('permission:ver_reportes_dependencia|ver_reportes_general|ver_reportes_operativos');

          Route::get('/reservas', [ReservaController::class, 'reservas'])
              ->middleware('permission:ver_reservas')
              ->name('reservas.index');
    });


});


Route::middleware(['auth', 'role:Operativo'])
    ->prefix('operativo')
    ->name('operativo.')
    ->group(function () {

        Route::get('/dashboard',[UserController::class, 'dashboard2'])
        ->name('dashboard');



});




// RUTA DE PRUEBA API
Route::get('/test-combustible', function (CombustibleApiService $service) {
    return response()->json([
        'resultado' => $service->obtenerPrecioActual()
    ]);
})->name('test.combustible');
