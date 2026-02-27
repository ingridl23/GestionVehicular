<?php

namespace App\Http\Controllers\Reservas;
use App\Http\Requests\FiltroReservasRequest;
use App\Models\Dependencia;
use App\Models\Reserva;
use App\Services\Reservas\ReservasExternasService;

/**
 * @class PrestamoController
 * @brief Controlador encargado de la gestión de reservas externas
 *        (préstamos entre dependencias).
 *
 * Extiende BaseReservaController.
 * Implementa lógica específica para:
 * - Autorización de préstamos
 * - Rechazo
 * - Filtros externos
 *
 * @package App\Http\Controllers\Reservas
 */
class PrestamoController extends BaseReservaController{

    public function __construct(ReservasExternasService $service)
    {
        parent::__construct($service);
    }

/**
 * Muestra listado de reservas externas.
 *
 * @return \Illuminate\View\View
 */
    // permiso = ver_reservas_prestamos
    public function verReservas(){
        $this->authorize('viewAnyLoan', Reserva::class);
        $datos = $this->service->verReservas();
        $data = array_merge(
            ['reservas' => $datos['reservas']],
            ['ids' => $datos['ids']],
            ['total' => $datos['total']],
            $this->service->datosFiltros(),
            ['ubicacion' => 'externa'],
            ['mostrarAcciones' => true],
        );
        return view('ui.reservas.reservas', $data);
    }

/**
 * Muestra reservas externas pendientes de autorización.
 *
 * @return \Illuminate\View\View
 */
    // permission:ver_solicitudes_prestamos
    public function verReservasPendientes(){
        $this->authorize('ViewPendingLoans', Reserva::class);
        $datos = $this->service->verReservasPendientes();
        $data = array_merge(
            ['reservas' => $datos['reservas']],
            ['total' => $datos['total']],
            ['mostrarAcciones' => false],
            ['ubicacion' => 'autorizar'],
            $this->service->datosFiltros(),
        );


        return view('ui.reservas.reservasPendientes', $data);
    }

/**
 * Autoriza un préstamo entre dependencias.
 *
 * Maneja errores de negocio y devuelve respuesta JSON.
 *
 * @param int $id
 * @return \Illuminate\Http\JsonResponse
 */
    // permission:autorizar_prestamos
    public function autorizarPrestamo($id){
        $this->authorize('authorizeLoans', Reserva::findOrFail($id));
        $resultado = $this->service->autorizarPrestamo($id);

        if(is_array($resultado)){
            $mensaje = $this->mensajesErrores($resultado);
             return response()->json([
                'success' => false,
                'errors'  => true,
                'message' => array_values($mensaje)[0]
            ]);
        }

        if($resultado){
            return response()->json([
                'success' => true,
                'errors'  => false,
                'message' => 'El prestamo fue autorizado correctamente.'
            ]);
        }
        return response()->json([
            'success' => false,
            'errors' => false,
            'message' => 'No se logro autorizar el préstamo, intentelo nuevamente.'
        ]);

    }

/**
 * Rechaza un préstamo.
 *
 * @param int $id
 * @return \Illuminate\Http\JsonResponse
 */
    // permission:rechazar_prestamos
    public function rechazarPrestamo($id){
        $this->authorize('rejectLoans', Reserva::findOrFail($id));
       $resultado = $this->service->rechazarPrestamo($id);

        if($resultado){
            return response()->json([
                'success' => true,
                'message' => 'El prestamo fue rechazado correctamente.'
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'No se logro rechazar el préstamo, intentelo nuevamente.'
        ]);
    }


    /**
     * Filtra las reservas externas según el rol del usuario autenticado.
     *
     * - Administrador de Dependencia y Jefe de Área:
     *   Obtienen todas las reservas externas vinculadas a su dependencia.
     *
     * - Operativo:
     *   Obtiene únicamente las reservas externas de su dependencia
     *   donde el conductor designado sea el usuario.
     *
     * - Administrador General:
     *   Obtienen todas las reservas externas sin restricción por dependencia.
     *
     * Además, se obtienen los IDs del árbol de dependencias asociado al usuario
     * para enviarlos en la respuesta. Finalmente, la consulta base se procesa
     * mediante el método filtrarReservas() para aplicar filtros adicionales
     * provenientes del request y se retorna la respuesta en formato JSON.
     *
     * @param FiltroReservasRequest $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function filtrarReservasExternas(FiltroReservasRequest $request){
        $rol = $this->service->rol();
        $id_dependencia = $this->service->user()->dependencia->id;
        $ids = $this->service->obtenerDependenciasIds(Dependencia::find($id_dependencia));

        $query = Reserva::with('estado_reserva', 'vehiculo', 'usuario', 'dependencia_solicitante')->orderBy('fecha_inicio_reserva');

        if($rol == 'Administrador de Dependencia' || $rol == 'Jefe de Area'){
            $query->obtenerDependenciasExternas($id_dependencia);
        }

        else if($rol == 'Operativo'){
            $query->obtenerDependenciasExternas($id_dependencia)->where('id_usuario', $this->service->user()->id);
        }
        else{
            $query->soloExternas();
        }
        $paginado = $this->filtrarReservas($request, $query);

        return response()->json([
        'data' => $paginado->items(),
        'meta' => [
            'current_page' => $paginado->currentPage(),
            'last_page' => $paginado->lastPage(),
            'per_page' => $paginado->perPage(),
            'total' => $paginado->total(),
        ],
        'ids' => $ids
    ]);
    }


    /**
     * Filtra las reservas pendientes de autorización de préstamos según el rol del usuario autenticado.
     *
     * - Si el usuario es Administrador General, obtiene todas las reservas externas
     *   en estado PENDIENTE.
     * - Si el usuario es Administrador de Dependencia, obtiene únicamente las reservas
     *   externas pendientes vinculadas a su dependencia.
     *
     * La consulta se construye dinámicamente aplicando los scopes correspondientes
     * y luego se envía al método filtrarReservas() para aplicar filtros adicionales
     * provenientes del request.
     *
     * @param FiltroReservasRequest $request
     * @return mixed
     */

    public function filtrarAutorizarPrestamos(FiltroReservasRequest $request){
        $rol = $this->service->rol();
        $id_dependencia = $this->service->user()->dependencia->id;
        $query = Reserva::with('estado_reserva', 'vehiculo', 'usuario', 'dependencia_solicitante')->orderBy('fecha_inicio_reserva');

        if($rol == 'Administrador General'){
            $query->soloExternas()->whereIn('id_estado_reserva', function ($sub) {
            $sub->select('id')
                ->from('estados_reservas')
                ->whereIn('estado', ['PENDIENTE']);
            });
        }
        if($rol == 'Administrador de Dependencia'){
            $query->obtenerDependenciasExternasPendientes($id_dependencia)->whereIn('id_estado_reserva', function ($sub) {
            $sub->select('id')
                ->from('estados_reservas')
                ->whereIn('estado', ['PENDIENTE']);
            });
        }
        return $this->filtrarReservas($request, $query);
    }


/**
 * Retorna préstamos externos paginados en formato JSON.
 *
 * @return \Illuminate\Http\JsonResponse
 */
    public function verPrestamosExternos(){
        $id_dependencia = $this->service->user()->dependencia->id;

        $ids = $this->service->obtenerDependenciasIds(Dependencia::find($id_dependencia));

        $paginado = $this->service->verPrestamosExternos();


       return response()->json([
        'data' => $paginado->items(),
        'meta' => [
            'current_page' => $paginado->currentPage(),
            'last_page' => $paginado->lastPage(),
            'per_page' => $paginado->perPage(),
            'total' => $paginado->total(),
        ],
        'ids' => $ids
    ]);
    }

/**
 * Retorna préstamos internos paginados en formato JSON.
 *
 * @return \Illuminate\Http\JsonResponse
 */
    public function verPrestamosInternos(){
        $id_dependencia = $this->service->user()->dependencia->id;

        $paginado = $this->service->verPrestamosInternos();


        $ids = $this->service->obtenerDependenciasIds(Dependencia::find($id_dependencia));

        return response()->json([
        'data' => $paginado->items(),
        'meta' => [
            'current_page' => $paginado->currentPage(),
            'last_page' => $paginado->lastPage(),
            'per_page' => $paginado->perPage(),
            'total' => $paginado->total(),
        ],
        'ids' => $ids
    ]);

    }

}
