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
        $reserva = Reserva::findOrFail($id);
        //$this->authorize('actualizar', $reserva);
        return view('ui.reservas.formularios.editar', $this->service->datosParaFormEditar($id));
    }


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
            // $query->whereHas('direccion', function ($q) use ($localidad) {
            //     $q->where('ciudad', $localidad);
            // });
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


        $reservas = $query->get();

        return response()->json(['reservas' => $reservas]);
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
 