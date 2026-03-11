<?php

namespace App\Http\Controllers\Reservas;

use App\Http\Requests\FiltroReservasRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReservasExport;
use App\Models\Reserva;
use App\Services\Reservas\ReservasInternasService;

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
 */




class ReservaController extends BaseReservaController{


    public function __construct(ReservasInternasService $service)
    {
        parent::__construct($service);
    }


    /**
 * Muestra listado de reservas internas.
 *
 * @return \Illuminate\View\View
 */
    // permiso = ver_reservas_internas
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
     */
public function misReservas()
{
    $this->authorize('viewAny', Reserva::class);

    $reservas = Reserva::where('id_usuario', auth()->id())
        ->orderBy('fecha_inicio_reserva')
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
            'id_usuario' => 'required|integer|min:1|exists:users,id',
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


// permission: autorizar_reservas_internas
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

public function exportarReservas()
{
    $reservas = $this->service->reservasParaExport();

    return Excel::download(
        new ReservasExport($reservas),
        'reservas.xlsx'
    );
}
}
