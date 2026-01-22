<?php

namespace App\Http\Controllers;

use App\Http\Requests\FiltroReservasRequest;
use App\Models\Reserva;
use App\Services\ReservaService;
use App\Policies\ReservaPolicy;
use Illuminate\Support\Facades\Auth;

class ReservaController extends Controller{
    protected ReservaService $service;

    public function __construct(ReservaService $service)
    {
        $this->service = $service;
    }

    // permiso = ver_reservas_internas
    public function verReservasInternas(){
        $this->authorize('viewAny', Reserva::class);
        $data = array_merge(
            ['reservas' => $this->service->verReservasInternas()],
            $this->service->datosFiltrosInternas(),
            ['ubicacion' => 'interna'],
        );
        return view('ui.reservas.reservas', $data);
    }

    // permiso = ver_reservas_prestamos
    public function verReservasExternas(){
        $data = array_merge(
            ['reservas' => $this->service->verReservasExternas()],
            $this->service->datosFiltrosExternas(),
            ['ubicacion' => 'externa'],
        );
        return view('ui.reservas.reservas', $data);
    }


    // permiso = ver_reservas_internas
    public function verReserva($id){
        $reserva = $this->service->verReserva($id, Auth::user());
        //$this->authorize('view', $reserva); No toma $reserva
        return view('ui.reservas.reserva', $reserva);
    }


    // permiso = cancelar_reserva_interna || 'cancelar_prestamo'
    public function cancelarReserva($id){
        //$this->authorize('cancelar');
        $this->service->cancelarReserva($id);
         return response()->json([
        'success' => true,
        'message' => 'La reserva fue cancelada correctamente'
    ]);
        //return redirect()->route('reservas.internas')->with('success', 'La dependencia fue cancelada correctamente.');
    }



    public function filtrarReservasInternas(FiltroReservasRequest $request){
        $rol = $this->service->rol();
        $id_dependencia = $this->service->user()->dependencia->id;
        $query = Reserva::with('estado_reserva', 'vehiculo', 'usuario', 'dependencia_solicitante')->orderBy('fecha_inicio_reserva');


        if($rol == 'Dueño Dependencia' || $rol == 'Jefe de Area'){
            $query->obtenerDependenciasInternas($id_dependencia);
        }

        else if($rol == 'Operativo'){
            $query->obtenerDependenciasInternas($id_dependencia)->where('id_usuario', $this->service->user()->id);
        }
        else{
            $query->soloInternas();
        }
        return $this->filtrarReservas($request, $query);
    }

    public function filtrarReservasExternas(FiltroReservasRequest $request){
        $rol = $this->service->rol();
        $id_dependencia = $this->service->user()->dependencia->id;
        $query = Reserva::with('estado_reserva', 'vehiculo', 'usuario', 'dependencia_solicitante')->orderBy('fecha_inicio_reserva');

        if($rol == 'Dueño Dependencia' || $rol == 'Jefe de Area'){
            $query->obtenerDependenciasExternas($id_dependencia);
        }

        else if($rol == 'Operativo'){
            $query->obtenerDependenciasExternas($id_dependencia)->where('id_usuario', $this->service->user()->id);
        }
        else{
            $query->soloExternas();
        }
        return $this->filtrarReservas($request, $query);
    }

     // permiso = filtrar_reservas_internas
     // permiso = filtrar_prestamos
    public function filtrarReservas($request, $query){

        /* ----------------------
         FILTRO POR NOMBRE DE LA DEPENDENCIA SOLICITANTE O POR NOMBRE O APELLIDO DEL CONDUCTOR
        ---------------------- */

        //filled se fija que exista y no este vacio
        if(!empty($request->filled('nombre')) && $request->input('nombre') != ''){
            $nombre = $request->input('nombre');
            $query->where(function ($q) use ($nombre) {

            // Dependencia solicitante
            $q->whereHas('dependencia_solicitante', function ($q2) use ($nombre) {
                $q2->where('nombre', 'LIKE', "%{$nombre}%");
            })

            // OR Usuario
            ->orWhereHas('usuario', function ($q3) use ($nombre) {
                $q3->where('name', 'LIKE', "%{$nombre}%")
                ->orWhere('lastname', 'LIKE', "%{$nombre}%");
            });

        });
        }

        /* ----------------------
         FILTRO POR SI EL ESTADO DE LA RESERVA
        ---------------------- */

        if (!empty($request->filled('estado')) && $request->input('estado') != 'default') {
            $activa = $request->input('estado');
            $query->where('id_estado_reserva', $activa);
        }

        /* ----------------------
         VEHICULO
        ---------------------- */

        if (!empty($request->filled('vehiculo')) && $request->input('vehiculo') != 'default') {
            $vehiculo = $request->input('vehiculo');
            $query->where('id_vehiculo', $vehiculo);
            // $query->whereHas('direccion', function ($q) use ($localidad) {
            //     $q->where('ciudad', $localidad);
            // });
        }   

        /* ----------------------
         FECHA DE INICIO
        ---------------------- */

        if (!empty($request->filled('fecha_inicio')) && $request->input('fecha_inicio') != '') {
            $fecha_inicio = $request->input('fecha_inicio');
            $query->whereDate('fecha_inicio_reserva', $fecha_inicio);
        }   

        /* ----------------------
         FECHA DE FIN
        ---------------------- */

        if (!empty($request->filled('fecha_fin')) && $request->input('fecha_fin') != '') {
            $fecha_fin = $request->input('fecha_fin');
            $query->whereDate('fecha_fin_reserva', $fecha_fin);
        }   

        /* ----------------------
         ORDENAMIENTO SEGURO
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
        
        /* ----------------------
         PAGINACIÓN
        ---------------------- */
       $dependencias = $query->paginate(10);

        return response()->json($dependencias);
    }


}
