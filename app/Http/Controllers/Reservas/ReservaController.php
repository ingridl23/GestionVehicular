<?php

namespace App\Http\Controllers\Reservas;

use App\Http\Requests\FiltroReservasRequest;
use App\Http\Requests\ReservaFormRequest;
use App\Models\Reserva;
use App\Services\Reservas\ReservasInternasService;

class ReservaController extends BaseReservaController{


    public function __construct(ReservasInternasService $service)
    {
        parent::__construct($service);
    }

    // permiso = ver_reservas_internas
    public function verReservas(){
        $this->authorize('viewAny', Reserva::class);
        $data = array_merge(
            ['reservas' => $this->service->verReservas()],
            $this->service->datosFiltros(),
            ['ubicacion' => 'interna'],
        );

        return view('ui.reservas.reservas', $data);
    }


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
        return $this->filtrarReservas($request, $query);
    }



}