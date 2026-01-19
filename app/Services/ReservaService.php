<?php

namespace App\Services;

use App\Models\EstadosReserva;
use App\Models\Reserva;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\Auth;

class ReservaService{

    protected DireccionService $direccionService;

    public function __construct(DireccionService $direccionService)
    {
        $this->direccionService = $direccionService;
    }

    protected function user(){
        return Auth::user();
    }

    protected function rol(){
        return $this->user() ? mb_strtolower($this->user()->rol, 'UTF-8') : null;
    }

    public function verReservas(){
        $rol = $this->rol();
        $id_dependencia = $this->user()->dependencia;
        $query = Reserva::with('estado_reserva', 'vehiculo')->orderBy('fecha_inicio_reserva');

        // Solo puede ver las  reservas que involucran a la dependencia
        if($rol == 'administrador de dependencia' || $rol == 'jefe de oficina'){
           $query->where(function ($q) use ($id_dependencia) {
                $q->where('id', $id_dependencia)
                ->orWhere('id_dependencia_duena', $id_dependencia)
                ->orWhere('id_dependencia_solicitante', $id_dependencia);
            });
        }
        else if($rol == 'conductor'){
            //Ver si trae las reservas relacionandas o no
            //Falta paginacion (ver si se incluye aca o en otro lado)
            $reservas = $this->user()->reservas;
            return $reservas;
        }

        $reservas = $query->paginate(10);
        return $reservas;
    }


    public function verReserva($id){
        
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

    public function datosFiltros(){
        return [
            'vehiculos_filtros' => Vehiculo::whereHas('reservas')->orderBy('dominio')->get(),
        ];
    }
}