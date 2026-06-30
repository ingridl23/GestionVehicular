<?php

namespace App\Http\Controllers\Reservas;
use App\Http\Requests\FiltroReservasRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReservasExport;
use App\Models\Reserva;
use App\Models\EstadosReserva;
use App\Models\EstadosVehiculo;
use App\Models\Viaje;
use App\Services\Reservas\ReservasInternasService;
use App\Http\Controllers\Reservas\BaseReservaController;
use Illuminate\Http\Request;

/**
 * @class ReservaController
 * @brief Controlador de reservas internas dentro de la misma dependencia.
 *
 * Extiende BaseReservaController.
 *
 * Implementa:
 * - Listado
 * - Filtros internos
 * - Cambio de conductor
 *
 * @package App\Http\Controllers\Reservas
 * @since 2026
 */


class ReservaController extends BaseReservaController{

/**
 * Constructor.
 *
 * @param ReservasInternasService $service Servicio de gestión de reservas internas.
 */
    public function __construct(ReservasInternasService $service)
    {
        parent::__construct($service);
    }


/**
 * Muestra listado de reservas internas.
 *
 * @return \Illuminate\View\View
// permission = ver_reservas_internas
 */
    public function verReservas(){
        $this->authorize('viewAny', Reserva::class);
        $datos = $this->service->verReservas();
        $botones = $this->configurarBotones('admin', 'interna');
        //dd($botones);
  $data = array_merge(
    [
        'reservas' => $datos['reserva'],
        'ids' => null,
        'total' => $datos['total'],
        'contexto' => 'admin',
    ],
    $this->service->datosFiltros(),
    [
        'ubicacion' => 'interna',
        'mostrarAcciones' => true,
    ],
    $botones
);

        return view('ui.reservas.reservas', $data);
    }



  /**
   * Muestra una reserva
   *
   * @param int $id
   * @return \Illuminate\View\View
   *
   * permission = ver_reserva
   *
   *  */
public function show($id)
{
    $reserva = Reserva::with([
        'vehiculo.estadoNafta',
        'estado_reserva',
        'dependencia_duena.direccion',
        'dependencia_solicitante.direccion',
        'usuario'
    ])->findOrFail($id);

    $this->authorize('view', $reserva);

    $vtv = $reserva->vehiculo->vigente;
    $carnet_vigente = $reserva->usuario->carnet_vigente; // idem

    return view('ui.reservas.reserva', compact(
        'reserva',
        'vtv',
        'carnet_vigente'
    ));
}



    /**
     * Muestra reservas propias del usuario que se encuentra logueado
     * @return \Illuminate\View\View
     *
     */
  public function misReservas(){
    $this->authorize('viewAny', Reserva::class);

    $reservas = Reserva::with(['estado_reserva', 'vehiculo', 'usuario', 'dependencia_solicitante', 'viajeActivo'])
        ->where('id_usuario', auth()->id())
        ->orderByRaw("
            CASE (
                SELECT estado FROM estados_reservas
                WHERE estados_reservas.id = reserva.id_estado_reserva
            )
                WHEN 'APROBADA'   THEN 1
                WHEN 'EN_CURSO'   THEN 2
                WHEN 'SOLICITADA' THEN 3
                WHEN 'FINALIZADA' THEN 4
                WHEN 'CANCELADA'  THEN 5
                WHEN 'RECHAZADA'  THEN 6
                ELSE 99
            END
        ")
        ->orderBy('fecha_inicio_reserva', 'desc')
        ->paginate(10);
   $botones = $this->configurarBotones('usuario', 'interna');

$data = array_merge(
    [
        'reservas' => $reservas,
        'ids' => null,
        'total' => $reservas->total(),
        'contexto' => 'usuario',
    ],
    $this->service->datosFiltros(),
    [
        'ubicacion' => 'interna',
        'mostrarAcciones' => true,
    ],
    $botones
);

    return view('ui.reservas.reservas', $data);
}


    /**
     * Filtra las reservas internas según el rol del usuario autenticado.
     *
     * - Administrador de Dependencia y Jefe de Área:
     *   Obtienen todas las reservas internas vinculadas a su dependencia o sus hijas.
     *
     * - Operativo:
     *   Obtiene únicamente las reservas internas de su dependencia
     *   donde el conductor designado sea el usuario.
     *
     * - Administrador General:
     *   Obtienen todas las reservas internas sin restricción por cada dependencia.
     *
     * Finalmente, la consulta base se procesa mediante el método filtrarReservas() para aplicar filtros adicionales
     * provenientes del request y se retorna la respuesta en formato JSON.
     *
     * @param FiltroReservasRequest $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function filtrarReservasInternas(FiltroReservasRequest $request){
        $rol = $this->service->rol();
        $id_dependencia = $this->service->user()->dependencia->id;
        $query = Reserva::with('estado_reserva', 'vehiculo', 'usuario', 'dependencia_solicitante')->orderBy('fecha_inicio_reserva');


        if($rol == 'Administrador de Dependencia' || $rol == 'Jefe de Area'){
            $query->obtenerDependenciasInternas($id_dependencia);
        }

        else if($rol == 'Operativo'){
            $query->obtenerDependenciasInternas($id_dependencia)->where('id_usuario', $this->service->user()->id);
        }
        else{
            $query->soloInternas();
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
    ]);
    }


    /**
     * Muestra el formulario para editar el conductor (usuario) asignado a una reserva.
     *
     * Primero valida la autorización del usuario autenticado mediante la policy
     * correspondiente ('cambiarConductor'). Luego obtiene la reserva y los datos
     * necesarios para el formulario (como la lista de usuarios disponibles).
     *
     * Finalmente, retorna la vista con la información necesaria para permitir
     * la modificación del conductor asignado.
     *
     * @param int $id ID de la reserva
     * @return \Illuminate\View\View
     */
    public function formularioEditarConductor($id){

        $this->authorize('cambiarConductor', Reserva::findOrFail($id));

        $reserva = Reserva::findOrFail($id);
        $datos = $this->service->datosParaFormEditar($id);
        $usuarios = $datos['usuarios'];
        $id_usuario_reserva = $reserva->id_usuario;
        $id = $reserva->id;

        return view('operativo.editarConductor', compact('usuarios', 'id', 'id_usuario_reserva'));
    }


/**
 * Actualiza el conductor asignado a una reserva.
 *
 * @param \Illuminate\Http\Request $request
 * @param int $id
 * @return \Illuminate\Http\RedirectResponse
 */
    public function editarConductor(Request $request, $id){

        $request->validate([
            'id_usuario' => 'required|integer|min:1|exists:user,id',
        ],
        [
            'id_usuario.required' => 'Debe seleccionar un conductor.',
            'id_usuario.integer'  => 'El conductor seleccionado no es válido.',
            'id_usuario.min'      => 'Debe seleccionar un conductor válido.',
            'id_usuario.exists'   => 'El conductor seleccionado no existe en el sistema.',
        ]);

        $resultado = $this->service->editarConductor($request, $id);

        if (!empty($resultado) && $resultado[1]) {
            return $this->mensajes($resultado);
        }

         return redirect()
        ->route('reservas.internas');
    }


    /**
     * Devuelve el vehículo cerrando la reserva (APROBADA → FINALIZADA).
     * Solo aplica cuando no hay viaje activo en curso.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     *
     * permission = finalizar_reserva
     *
     */
    public function finalizarReserva($id)
    {
        $reserva = Reserva::with(['vehiculo', 'estado_reserva'])->findOrFail($id);

        if (auth()->user()->hasRole('Operativo') && $reserva->id_usuario !== auth()->id()) {
            abort(403);
        }

        if ($reserva->id_estado_reserva !== EstadosReserva::APROBADA) {
            return back()->withErrors(['error' => 'Solo se puede devolver un vehículo con reserva aprobada.']);
        }

        $tieneViajeActivo = Viaje::where('id_reserva', $id)->whereNull('fecha_fin')->exists();
        if ($tieneViajeActivo) {
            return back()->withErrors(['error' => 'Hay un viaje en curso. Finalizalo antes de devolver el vehículo.']);
        }

        $reserva->update(['id_estado_reserva' => EstadosReserva::FINALIZADA]);

        $idDisponible = EstadosVehiculo::where('estado', 'DISPONIBLE')->value('id');
        $reserva->vehiculo->update(['id_estado_vehiculo' => $idDisponible]);

        $ruta = auth()->user()->hasRole('Operativo') ? 'operativo.mis-reservas' : 'reservas.internas';

        return redirect()->route($ruta)->with('success', 'Vehículo devuelto correctamente.');
    }


    /**
     * Autoriza una reserva interna.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     *
     * permission: autorizar_reservas_internas
     *
     *
     */
public function autorizarReserva($id) {
    $this->authorize('authorizeInternalReservation', Reserva::findOrFail($id));
    $resultado = $this->service->autorizarReserva($id);

    if (is_array($resultado)) {
        $mensaje = $this->mensajesErrores($resultado);
        return response()->json([
            'success' => false,
            'errors'  => true,
            'message' => array_values($mensaje)[0]
        ]);
    }

    if ($resultado) {
        return response()->json([
            'success' => true,
            'message' => 'La reserva fue aprobada correctamente.'
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'No se pudo aprobar la reserva.'
    ]);
}

/**
 * EXPORTAR RESERVAS
 *
 * permission: exportar_reservas
 * permite al usuario exportar las reservas de los ultimos 4 meses
 *
 */
public function exportarReservas()
{
    $reservas = $this->service->reservasParaExport();

    return Excel::download(
        new ReservasExport($reservas),
        'reservas_ult4meses.xlsx'
    );
}
}
