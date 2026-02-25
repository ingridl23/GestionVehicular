<?php

namespace App\Http\Controllers\Reservas;

use App\Contracts\ReservaServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReservaFormRequest;
use App\Models\Reserva;
use Illuminate\Support\Facades\Auth;

abstract class BaseReservaController extends Controller
{

    protected ReservaServiceInterface $service;

    public function __construct(ReservaServiceInterface $service)
    {
        $this->service = $service;
    }

    // permiso = ver_reservas_internas || ver_reservas_prestamos
    public function verReserva($id)
    {
        //$this->authorize('vistaIndividual', Reserva::findOrFail($id));
        $reserva = $this->service->verReserva($id, Auth::user());
        return view('ui.reservas.reserva', $reserva);
    }

    // permiso = cancelar_reserva_interna || 'cancelar_prestamo'
    public function cancelarReserva($id)
    {
        $reserva = Reserva::findOrFail($id);
        $this->authorize('cancelar', $reserva);
        $this->service->cancelarReserva($id);
        return response()->json([
            'success' => true,
            'message' => 'La reserva fue cancelada correctamente'
        ]);
    }

    //permiso = 'solicitar_prestamo' || 'solicitar_reserva_interna'
    public function mostrarFormulario()
    {
        $this->authorize('create', Reserva::class);
        return view('ui.reservas.formularios.crear', $this->service->datosParaFormCrear());
    }


    //permiso = 'actualizar_prestamo' || 'actualizar_reserva_interna'
    public function mostrarFormularioUpdate($id)
    {
        //$this->authorize('actualizar', $reserva);
        return view('ui.reservas.formularios.editar', $this->service->datosParaFormEditar($id));
    }


    /**
     * Aplica filtros dinámicos a la consulta de reservas según los parámetros
     * recibidos en el request y retorna el resultado en formato JSON.
     *
     * Filtros disponibles:
     *  - Nombre: busca por nombre o apellido del conductor (usuario)
     *    o por nombre de la dependencia solicitante.
     *  - Estado: filtra por ID de estado de la reserva.
     *  - Vehículo: filtra por ID de vehículo.
     *  - Fecha de inicio: filtra por fecha exacta de inicio de reserva.
     *  - Fecha de fin: filtra por fecha exacta de fin de reserva.
     *
     * Además, permite ordenar los resultados dinámicamente mediante:
     *  - sort_field (campo de ordenamiento)
     *  - sort_order (asc o desc)
     *
     * Solo se permiten campos y órdenes definidos en listas
     * para evitar valores inválidos.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Http\JsonResponse
     */
    // permiso = filtrar_reservas_internas || filtrar_prestamos
    public function filtrarReservas($request, $query){

        /* ----------------------
         FILTRO POR NOMBRE DE LA DEPENDENCIA SOLICITANTE O POR NOMBRE O APELLIDO DEL CONDUCTOR
        ---------------------- */

        //filled se fija que exista y no este vacio
        if ($request->filled('nombre')) {

            $nombre = mb_strtolower(trim($request->nombre));

            $query->where(function ($q) use ($nombre) {

                // Buscar en usuario
                $q->whereHas('usuario', function ($q2) use ($nombre) {
                    $q2->where(function ($sub) use ($nombre) {
                        $sub->whereRaw("LOWER(name) LIKE ?", ["%{$nombre}%"])
                            ->orWhereRaw("LOWER(lastname) LIKE ?", ["%{$nombre}%"]);
                    });
                })

                // O buscar en dependencia solicitante
                ->orWhereHas('dependencia_solicitante', function ($q3) use ($nombre) {
                    $q3->whereRaw("LOWER(nombre) LIKE ?", ["%{$nombre}%"]);
                });

            });
        }


        /* ----------------------
         FILTRO POR EL ESTADO DE LA RESERVA
        ---------------------- */

        if ($request->filled('estado') && $request->input('estado') != 'default') {
            $activa = $request->input('estado');
            $query->where('id_estado_reserva', $activa);
        }

        /* ----------------------
         VEHICULO
        ---------------------- */

        if ($request->filled('vehiculo') && $request->input('vehiculo') != 'default') {
            $vehiculo = $request->input('vehiculo');
            $query->where('id_vehiculo', $vehiculo);
        }

        /* ----------------------
         FECHA DE INICIO
        ---------------------- */

        if ($request->filled('fecha_inicio') && $request->input('fecha_inicio') != '') {
            $fecha_inicio = $request->input('fecha_inicio');
            $query->whereDate('fecha_inicio_reserva', $fecha_inicio);
        }

        /* ----------------------
         FECHA DE FIN
        ---------------------- */

        if ($request->filled('fecha_fin') && $request->input('fecha_fin') != '') {
            $fecha_fin = $request->input('fecha_fin');
            $query->whereDate('fecha_fin_reserva', $fecha_fin);
        }

        /* ----------------------
         ORDENAMIENTO
        ---------------------- */

        //Campo por el que se ordena
        $sortField = $request->input('sort_field', 'fecha_inicio_reserva');

        //Como se ordena
        $sortOrder = $request->input('sort_order', 'asc');

        $allowedSorts = ['fecha_inicio_reserva', 'fecha_reserva'];
        $allowedOrders = ['asc', 'des'];

        if (!in_array($sortField, $allowedSorts)) {
            $sortField = 'fecha_inicio';
        }

        if (!in_array($sortOrder, $allowedOrders)) {
            $sortOrder = 'asc';
        }


        $query->orderBy($sortField, $sortOrder);


        return $query->paginate(10);
    }

    //permiso = 'solicitar_reserva_interna' || 'solicitar_prestamo',
    public function crearReserva(ReservaFormRequest $request){

        $this->authorize('create', Reserva::class);

        $resultado = $this->service->crearReserva($request);

        if (!empty($resultado) && $resultado[1]) {
            return $this->mensajes($resultado);
        }

        return match ($request->tipo_reserva) {
            'interna' => redirect()->route('reservas.internas'),
            'prestamo' => redirect()->route('reservas.prestamos'),
        };

    }

    /**
     * Actualiza una reserva existente.
     *
     * Busca la reserva por su ID y delega la lógica de actualización al service.
     * Si el service retorna un resultado indicando error de validación de negocio,
     * se devuelven los mensajes correspondientes.
     *
     * En caso de éxito, redirige según el tipo de reserva:
     *  - "interna": redirige al listado de reservas internas.
     *  - "prestamo": redirige al listado de préstamos entre dependencias.
     *
     * @param ReservaFormRequest $request Datos validados del formulario.
     * @param int $id ID de la reserva a editar.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    //permiso = 'actualizar_reserva_interna' || 'actualizar_prestamo',
    public function editarReserva(ReservaFormRequest $request, $id)
    {
        $reserva = Reserva::findOrFail($id);
        //$this->authorize('actualizar', $reserva);

        $resultado = $this->service->editarReserva($request, $id);

        if (!empty($resultado) && $resultado[1]) {
            return $this->mensajes($resultado);
        }

        return match ($request->tipo_reserva) {
            'interna' => redirect()->route('reservas.internas'),
            'prestamo' => redirect()->route('reservas.prestamos'),
        };
    }


    public function mensajes($resultado){
        return back()
        ->withErrors($this->mensajesErrores($resultado))
        ->withInput();
    }


    /**
     * Devuelve los mensajes de error personalizados según el resultado
     * de las validaciones al intentar crear o modificar una reserva.
     *
     * Evalúa el tipo de error recibido en el arreglo $resultado y retorna
     * un array asociativo donde:
     *  - La clave corresponde al campo del formulario que contiene el error.
     *  - El valor es el mensaje descriptivo que se mostrará al usuario.
     *
     * Los posibles errores contemplados incluyen:
     *  - Usuario no disponible en el rango de fechas.
     *  - Usuario no habilitado como conductor.
     *  - Dependencia inválida o fuera del sector correspondiente.
     *  - Error en préstamo entre dependencias.
     *  - Vehículo no habilitado o no disponible.
     *
     * @param array $resultado Resultado de la validación de reglas de negocio.
     * @return array Mensajes de error asociados a los campos del formulario.
     */
    protected function mensajesErrores($resultado){
        if ($resultado[0] == "usuario") {
            return [
                'id_usuario' => 'El usuario no se encuentra disponible en el rango de fechas seleccionado.'
            ];
        } else if ($resultado[0] == "usuario_no_habilitado") {
            return [
                'id_usuario' => 'El usuario no posee carnet vigente o licencia para ser designado conductor.'
            ];
        } else if ($resultado[0] == "dependencia") {
            return [
                'id_dependencia' => 'La dependencia seleccionada no es valida ya que no pertenece al sector del usuario que desea reservar.'
            ];
        }
        else if ($resultado[0] == "dependencia_prestamo") {
            return [
                'id_dependencia' => 'La dependencia seleccionada no es valida ya que no corresponde a un préstamo entre dependencias, si desea generar una reserva interna, dirigirse a la ventana de "Internas".'
            ];
        }
         else if ($resultado[0] == "vehiculo_no_habilitado") {
            return [
                'id_vehiculo' => 'El vehiculo no se encuentra disponible para ser reservado.'
            ];
        }
        
        return [
            'id_vehiculo' => 'El vehiculo no se encuentra disponible en el rango de fechas seleccionado.'
        ];
    }
}
 