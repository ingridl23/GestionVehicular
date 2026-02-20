<?php

namespace App\Http\Controllers\Reservas;

use App\Http\Requests\FiltroReservasRequest;

use App\Models\Reserva;
use App\Services\Reservas\ReservasInternasService;
use Illuminate\Http\Request;

class ReservaController extends BaseReservaController{


    public function __construct(ReservasInternasService $service)
    {
        parent::__construct($service);
    }

    // permiso = ver_reservas_internas
    public function verReservas(){
        $this->authorize('viewAny', Reserva::class);
        $datos = $this->service->verReservas();
        $data = array_merge(
            ['reservas' => $datos['reservas']],
            ['ids' => null],
            ['total' => $datos['total']],
            $this->service->datosFiltros(),
            ['ubicacion' => 'interna'],
            ['mostrarAcciones' => true],
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

    public function formularioEditarConductor($id){

        $this->authorize('cambiarConductor', Reserva::findOrFail($id));
        
        $reserva = Reserva::findOrFail($id);
        $datos = $this->service->datosParaFormEditar($id);
        $usuarios = $datos['usuarios'];
        $id_usuario_reserva = $reserva->id_usuario;
        $id = $reserva->id;
       
        return view('operativo.editarConductor', compact('usuarios', 'id', 'id_usuario_reserva'));
    }

    public function editarConductor(Request $request, $id){

        $request->validate([
            'id_usuario' => 'required|integer|min:1|exists:users,id',
        ],
        [
            'id_usuario.required' => 'Debe seleccionar un conductor.',
            'id_usuario.integer'  => 'El conductor seleccionado no es válido.',
            'id_usuario.min'      => 'Debe seleccionar un conductor válido.',
            'id_usuario.exists'   => 'El conductor seleccionado no existe en el sistema.',
        ]);

        $resultado = $this->service->editarConductor($request, $id);

        if (!empty($resultado) && $resultado[1]) {
            return $this->mensajes($resultado);
        }

         return redirect()
        ->route('reservas.internas');
    }



}