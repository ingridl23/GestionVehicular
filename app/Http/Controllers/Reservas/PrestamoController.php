<?php

namespace App\Http\Controllers\Reservas;


use App\Http\Requests\CrearReservaRequest;
use App\Http\Requests\FiltroReservasRequest;
use App\Http\Requests\ReservaFormRequest;
use App\Models\Reserva;
use App\Services\Reservas\ReservasExternasService;


class PrestamoController extends BaseReservaController{

    public function __construct(ReservasExternasService $service)
    {
        parent::__construct($service);
    }


    // permiso = ver_reservas_prestamos
    public function verReservas(){
        $data = array_merge(
            ['reservas' => $this->service->verReservas()],
            $this->service->datosFiltros(),
            ['ubicacion' => 'externa'],
            ['mostrarAcciones' => true],
        );
        return view('ui.reservas.reservas', $data);
    }

    public function verReservasPendientes(){
        $data = array_merge(
            ['reservas' => $this->service->verReservasPendientes()],
            ['mostrarAcciones' => false],
            ['ubicacion' => 'externa'],
            $this->service->datosFiltros(),
        );


        return view('ui.reservas.reservasPendientes', $data);
    }




    public function filtrarReservasExternas(FiltroReservasRequest $request){
        $rol = $this->service->rol();
        $id_dependencia = $this->service->user()->dependencia->id;
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
        return $this->filtrarReservas($request, $query);
    }

}