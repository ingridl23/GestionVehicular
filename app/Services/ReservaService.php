<?php

namespace App\Services;

use App\Models\Carnet;
use App\Models\EstadosReserva;
use App\Models\Reserva;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class ReservaService{

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


    // El Administrador General puede ver todas las reservas independientemente de a que independencia pertenezca
    // El Administrador de Dependencia y Jefe de Oficina solo pueden ver las reservas que pertenecen a la Dependencia 
    // El Conductor visualiza las reservas donde esta involucrado (id_usuario)
    public function verReservasInternas(){
        $rol = $this->rol();
        $id_dependencia = $this->user()->dependencia->id;
        $query = Reserva::with('estado_reserva', 'vehiculo', 'usuario', 'dependencia_solicitante')->orderBy('fecha_inicio_reserva');


        if($rol == 'Dueño Dependencia' || $rol == 'Jefe de Area'){
            $query->obtenerDependenciasInternas($id_dependencia);
        }

        else if($rol == 'Operativo'){
            $query->obtenerDependenciasInternas($id_dependencia)->where('id_usuario', $this->user()->id);
        }
        else{
            $query->soloInternas();
        }

        $reservas = $query->paginate(10);
        return $reservas;
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
            return $reserva;
        }
        return null;
    }


    // El Administrador General puede ver todas las reservas independientemente de a que independencia pertenezca
    // El Administrador de Dependencia y Jefe de Oficina solo pueden ver las reservas que pertenecen a la Dependencia 
    // El Conductor visualiza las reservas donde esta involucrado
    public function verReservasExternas(){
        $rol = $this->rol();
        $id_dependencia = $this->user()->dependencia->id;
        $query = Reserva::with('estado_reserva', 'vehiculo', 'usuario', 'dependencia_solicitante')->orderBy('fecha_inicio_reserva');


        if($rol == 'Dueño Dependencia' || $rol == 'Jefe de Area'){
            $query->obtenerDependenciasExternas($id_dependencia);
        }

        else if($rol == 'Operativo'){
            $query->obtenerDependenciasExternas($id_dependencia)->where('id_usuario', $this->user()->id);
        }
        else{
            $query->soloExternas();
        }

        $reservas = $query->paginate(10);
        return $reservas;
    }



    public function cancelarReserva($id){
       $reserva = Reserva::findOrFail($id);

       // Se busca el id del estado : CANCELADA
        $estado_cancelado = EstadosReserva::select("id")->where("estado", "CANCELADA")->get();
        $reserva->update(['id_estado_Reserva' => $estado_cancelado]);
    }
    



    public function editarReserva(array $data, $id){
        
    }

    


    public function crearReserva(array $data){
        
    }

    public function datosFiltrosInternas(){
        $rol = $this->rol();
        $id_dependencia = $this->user()->dependencia->id;
        $id_usuario = $this->user()->id;

        //whereHas -> vehiculos que tengan al menos una reserva que cumpla con las condiciones (FILTRA)
        $query = Vehiculo::whereHas('reservas', function ($q) use ($rol, $id_dependencia, $id_usuario) {

            //Internas de su dependencia
            if ($rol === 'Dueño Dependencia' || $rol === 'Jefe de Area') {
                $q->obtenerDependenciasInternas($id_dependencia);
            } 

            // Sus reservas
            elseif ($rol === 'Operativo') {
                $q->obtenerDependenciasInternas($id_dependencia)->where('id_usuario', $id_usuario);
            } 

            // Todas las reservas que sean internas
            else {
                $q->soloInternas();
            }

        })
        //Carga las reservas de cada vehiculo pero que cumplan con las condiciones
        ->with(['reservas' => function ($q) use ($rol, $id_dependencia, $id_usuario) {

            if ($rol === 'Dueño Dependencia' || $rol === 'Jefe de Area') {
                $q->obtenerDependenciasInternas($id_dependencia);
            } 
            elseif ($rol === 'Operativo') {
                $q->obtenerDependenciasInternas($id_dependencia)
                ->where('id_usuario', $id_usuario);
            } 
            else {
                $q->soloInternas();
            }

        }]);

        return [
            'vehiculos_filtros' => $query->orderBy('dominio')->get(),
            'estados_filtros' => EstadosReserva::orderBy('estado')->get(),
        ];
    }



    public function datosFiltrosExternas(){
        $rol = $this->rol();
        $id_dependencia = $this->user()->dependencia->id;
        $id_usuario = $this->user()->id;

        //whereHas -> vehiculos que tengan al menos una reserva que cumpla con las condiciones (FILTRA)
        $query = Vehiculo::whereHas('reservas', function ($q) use ($rol, $id_dependencia, $id_usuario) {

            //Internas de su dependencia
            if($rol == 'Dueño Dependencia' || $rol == 'Jefe de Area'){
            $q->obtenerDependenciasExternas($id_dependencia)->groupBy('id_vehiculo');
            }

            else if($rol == 'Operativo'){
                $q->obtenerDependenciasExternas($id_dependencia)->where('id_usuario', $this->user()->id)->groupBy('id_vehiculo');
            }
            else{
                $q->soloExternas()->groupBy('id_vehiculo');
            }
        })
        //Carga las reservas de cada vehiculo pero que cumplan con las condiciones
        ->with(['reservas' => function ($q) use ($rol, $id_dependencia, $id_usuario) {

            if($rol == 'Dueño Dependencia' || $rol == 'Jefe de Area'){
            $q->obtenerDependenciasExternas($id_dependencia)->groupBy('id_vehiculo');
        }

            else if($rol == 'Operativo'){
                $q->obtenerDependenciasExternas($id_dependencia)->where('id_usuario', $this->user()->id)->groupBy('id_vehiculo');
            }
            else{
                $q->soloExternas()->groupBy('id_vehiculo');
            }

        }]);

        return [
            'vehiculos_filtros' => $query->orderBy('dominio')->get(),
            'estados_filtros' => EstadosReserva::orderBy('estado')->get(),
        ];
    }
}