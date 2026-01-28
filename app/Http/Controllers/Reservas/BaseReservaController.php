<?php

namespace App\Http\Controllers\Reservas;

use App\Contracts\ReservaServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\Reserva;
use Illuminate\Support\Facades\Auth;

abstract class BaseReservaController extends Controller{

    protected ReservaServiceInterface $service;

    public function __construct(ReservaServiceInterface $service)
    {
        $this->service = $service;
    }

    // permiso = ver_reservas_internas
    public function verReserva($id){
        $reserva = $this->service->verReserva($id, Auth::user());
        $this->authorize('vistaIndividual', $reserva['reserva']);
        return view('ui.reservas.reserva', $reserva);
    }

        // permiso = cancelar_reserva_interna || 'cancelar_prestamo'
    public function cancelarReserva($id){
        $reserva = Reserva::findOrFail($id);
        $this->authorize('cancelar', $reserva);
        $this->service->cancelarReserva($id);
        return response()->json([
            'success' => true,
            'message' => 'La reserva fue cancelada correctamente'
        ]);
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


        $query->orderBy($sortField, $sortOrder);

        /* ----------------------
         PAGINACIÓN
        ---------------------- */
       $reservas = $query->paginate(10);

        return response()->json($reservas);
    }

}
