<?php

namespace App\Services\reservas;

use App\Contracts\ReservaServiceInterface;
use App\Models\Carnet;
use App\Models\EstadosReserva;
use App\Models\Reserva;
use App\Models\User;
use App\Models\Vehiculo;
use App\Services\DireccionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

abstract class BaseReservasServices implements ReservaServiceInterface{

    protected DireccionService $direccionService;

    public function __construct(DireccionService $direccionService)
    {
        $this->direccionService = $direccionService;
    }

    public function user(){
        return Auth::user();
    }

    public function rol(){
        $rol = $this->user()->getRoleNames();
        return $rol[0] ;
    }


    protected function obtenerDatosBase($ids){
        $queryVehiculos = Vehiculo::join('estados_vehiculos', 'estados_vehiculos.id', '=', 'vehiculos.id_estado_vehiculo')
            ->join('dependencias', 'dependencias.id', '=', 'vehiculos.id_dependencia_duena')
            ->orderByRaw("
                CASE 
                    WHEN estados_vehiculos.estado = 'DISPONIBLE' THEN 0 
                    ELSE 1 
                END
            ")
            ->select('vehiculos.*', 'dependencias.nombre');

        $queryUsuarios = User::with('carnet')
            ->join('dependencias', 'dependencias.id', '=', 'users.id_dependencia')
            ->select('users.*', 'dependencias.nombre');

        return compact('queryVehiculos', 'queryUsuarios');
    }

    function obtenerDependenciasIds($dependencia){
        $ids = [$dependencia->id];

        foreach ($dependencia->dependenciasHijas as $hijo) {
            $ids = array_merge($ids, $this->obtenerDependenciasIds($hijo));
        }
        return $ids;
    }

    public function verReserva($id , User $user){
        if($user->can('ver_reservas_internas') || $user->can('ver_reservas_prestamos')){
            $reserva = Reserva::with('estado_reserva', 'vehiculo.nafta', 'usuario.carnet', 'dependencia_solicitante.direccion', 'dependencia_duena.direccion')
            ->find($id);
            $vtvVigente = Vehiculo::vtv_vigente($reserva->vehiculo->id);
            $carnetVigente = Carnet::carnetVigente($reserva->usuario->id);
            return [
                'reserva' => $reserva,
                'vtv'  => $vtvVigente,
                'carnet_vigente' => $carnetVigente,
            ];

        }
        return null;
    }

    public function cancelarReserva($id){
       $reserva = Reserva::findOrFail($id);
        if(!$reserva){
            return null;
        }
       
        // Se busca el id del estado : CANCELADA
        $estado_cancelado = EstadosReserva::where("estado", "CANCELADA")->value('id');
        $reserva->update(['id_estado_reserva' => $estado_cancelado]);
    }

    
    //Se valida que el vehiculo se encuentre disponible en ese rango de fechas
    //Se valida que el usuario este disponible en ese rango de fechas 
    public function crearReserva($request){
        $fecha_inicio = Carbon::createFromFormat('Y-m-d\TH:i', $request->input('fecha_inicio'));
        $fecha_fin = Carbon::createFromFormat('Y-m-d\TH:i', $request->input('fecha_fin'));
        $id_vehiculo = $request->id_vehiculo;
        $id_usuario = $request->id_usuario;
        
        $vehiculoOcupado = Reserva::where('id_vehiculo', $id_vehiculo)
            ->where(function ($q) use ($fecha_inicio, $fecha_fin) {
                $q->where('fecha_inicio_reserva', '<', $fecha_fin)
                ->where('fecha_fin_reserva', '>', $fecha_inicio);
            })->exists();

        if ($vehiculoOcupado) {
            return ["vehiculo" , true];
        }

        $usuarioOcupado = Reserva::where('id_usuario', $id_usuario)
            ->where(function ($q) use ($fecha_inicio, $fecha_fin) {
                $q->where('fecha_inicio_reserva', '<', $fecha_fin)
                ->where('fecha_fin_reserva', '>', $fecha_inicio);
            })->exists();

        if ($usuarioOcupado) {
            return ["usuario", true];
        }

        $id_dependencia_duena = Vehiculo::where('id', $id_vehiculo)->value('id_dependencia_duena');
        $id_dependencia_solicitante = User::where('id', $this->user()->dependencia->id)->value('id');
        $id_estado_reserva = $this->obtenerEstadoReserva();
        

        // solicitantes -> dejar seleccionar al usuario o poner el que le pertenece al usuario? 
        //Capaz mejor opcion la segunda
        //Crear reserva
        Reserva::create([
            'fecha_reserva'        => now(),
            'fecha_inicio_reserva' => $fecha_inicio,
            'fecha_fin_reserva'    => $fecha_fin,
            'id_vehiculo'          => $id_vehiculo,
            'id_estado_reserva'    => $id_estado_reserva,
            'id_usuario'           => $id_usuario,
            'id_dependencia_duena' => $id_dependencia_duena,
            'id_dependencia_solicitante' => $id_dependencia_solicitante,
        ]);
    }

    abstract public function obtenerEstadoReserva();
}
