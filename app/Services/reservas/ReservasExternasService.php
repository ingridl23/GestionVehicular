<?php

namespace App\Services\Reservas;

use App\Contracts\ReservaServiceInterface;
use App\Models\Dependencia;
use App\Models\EstadosReserva;
use App\Models\Reserva;

use App\Models\Vehiculo;
use App\Services\reservas\BaseReservasServices;


class ReservasExternasService extends BaseReservasServices implements ReservaServiceInterface{

    // El Administrador General puede ver todas las reservas independientemente de a que independencia pertenezca
    // El Administrador de Dependencia y Jefe de Oficina solo pueden ver las reservas que pertenecen a la Dependencia 
    // El Conductor visualiza las reservas donde esta involucrado
    public function verReservas(){
        $rol = $this->rol();
        $id_dependencia = $this->user()->dependencia->id;
        $query = Reserva::with('estado_reserva', 'vehiculo', 'usuario', 'dependencia_solicitante')
         ->orderByRaw("
            CASE (
            SELECT estado
            FROM estados_reservas
            WHERE estados_reservas.id = reservas.id_estado_reserva
        )
                WHEN 'APROBADA' THEN 1
                WHEN 'PENDIENTE'  THEN 2
                WHEN 'EN CURSO' THEN 3
                WHEN 'FINALIZADA' THEN 4
                WHEN 'CANCELADA' THEN 5
                WHEN 'RECHAZADA' THEN 6
                ELSE 99
            END
        ")->orderBy('fecha_inicio_reserva');


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


    public function datosParaFormEditar($id){
        $reserva = Reserva::findOrFail($id);

        $dependencia = Dependencia::with('hijosRecursivos')
            ->findOrFail($this->user()->dependencia->id);

        $ids = $this->obtenerDependenciasIds($dependencia);

        $base = $this->obtenerDatosBase($ids);

        $vehiculos = $base['queryVehiculos']
            ->whereNotIn('vehiculos.id_dependencia_duena', $ids)
            ->get();

        $usuarios = $base['queryUsuarios']->get();
        return [
            'vehiculos' => $vehiculos,
            'usuarios'  => $usuarios,
            'reserva'   => $reserva,
            'formAction' => route('reservas.externas.editar', $id),
        ];
        //return compact('vehiculos', 'usuarios', 'reserva');
    }



    //NO PUEDEN APARECER LOS VEHICULOS INTERNOS
    public function datosParaFormCrear(){
        $id_dependencia = $this->user()->dependencia->id;
        $dependencia = Dependencia::with('hijosRecursivos')->find($id_dependencia);

        $ids = $this->obtenerDependenciasIds($dependencia);

        $base = $this->obtenerDatosBase($ids);

        $vehiculos = $base['queryVehiculos']
            ->whereNotIn('vehiculos.id_dependencia_duena', $ids)
            ->get();

        $usuarios = $base['queryUsuarios']->get();

        return [
            'vehiculos' => $vehiculos,
            'usuarios'  => $usuarios,
            'formAction' => route('reservas.externas.crear'),
            'reserva'   => null,
        ];
    }

    public function obtenerEstadoReserva(){
        $id_estado_reserva = EstadosReserva::where("estado", "PENDIENTE")->value('id');
        return $id_estado_reserva;
    }


     //Datos que se muestran en los inputs de los filtros (los vehiculos que son usados por las dependencias que se muestran y
    // el estado de las reservas)
    public function datosFiltros(){
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