<?php

namespace App\Http\Controllers\Reservas;
use App\Http\Requests\FiltroReservasRequest;
use App\Models\Dependencia;
use App\Models\Reserva;
use App\Services\Reservas\ReservasExternasService;


class PrestamoController extends BaseReservaController{

    public function __construct(ReservasExternasService $service)
    {
        parent::__construct($service);
    }


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
        $response = $this->filtrarReservas($request, $query);

        $data = $response->getData(true);
        $data['ids'] = $ids;

        return response()->json($data);
    }



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


    public function verPrestamosExternos(){
        $id_dependencia = $this->service->user()->dependencia->id;
        
        $ids = $this->service->obtenerDependenciasIds(Dependencia::find($id_dependencia));

        $datos = $this->service->verPrestamosExternos();
        $data = $datos->getData(true);
        $data['ids'] = $ids;

        return response()->json($data);

    }

    public function verPrestamosInternos(){
        $id_dependencia = $this->service->user()->dependencia->id;
        
        $datos = $this->service->verPrestamosInternos();

        $ids = $this->service->obtenerDependenciasIds(Dependencia::find($id_dependencia));

        $data = $datos->getData(true);
        $data['ids'] = $ids;

        return response()->json($data);

    }

}