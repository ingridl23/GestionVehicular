<?php

namespace App\Http\Controllers\Reservas;


use App\Http\Requests\CrearReservaRequest;
use App\Http\Requests\FiltroReservasRequest;
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
        );
        return view('ui.reservas.reservas', $data);
    }



    //'solicitar_prestamo',
    public function mostrarFormulario(){ 
        $this->authorize('create', Reserva::class);
        return view('ui.reservas.formularios.crear', $this->service->datosParaFormCrear());
    }



    //'actualizar_prestamo'
    public function mostrarFormularioUpdate($id){ 
        $reserva = Reserva::findOrFail($id);
        $this->authorize('actualizar', $reserva);
        return view('ui.reservas.formularios.editar', $this->service->datosParaFormEditar($id));
       
    }
    

    public function crearReserva(CrearReservaRequest $request){
        $this->authorize('create', Reserva::class);
        $resultado = $this->service->crearReserva($request);


        if (!empty($resultado) && $resultado[1]) {
            if($resultado[0] == "usuario"){
                return back()->withErrors([
                    'id_usuario' => 'El usuario no se encuentra disponible en el rango de fechas seleccionado.'
                ]) ->withInput();;
            }
            return back()->withErrors([
                'id_vehiculo' => 'El vehiculo no se encuentra disponible en el rango de fechas seleccionado.'
            ]) ->withInput();;
        }

        return $this->verReservas();
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

}