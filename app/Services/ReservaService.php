<?php

namespace App\Services;

use App\Models\Carnet;
use App\Models\Dependencia;
use App\Models\EstadosReserva;
use App\Models\Reserva;
use App\Models\User;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

use function Symfony\Component\Clock\now;

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
        // El administrador de Dependencia y Jefe de Oficina puede ver las reservaqs que involucran a sus dependencias hijas
    // El Conductor visualiza las reservas donde esta involucrado (id_usuario)

    public function verReservasInternas(){
        $rol = $this->rol();
        $id_dependencia = $this->user()->dependencia->id;
        $query = Reserva::join('estados_reservas', 'estados_reservas.id', '=', 'reservas.id_estado_reserva')
        ->with('estado_reserva', 'vehiculo', 'usuario', 'dependencia_solicitante')
         ->orderByRaw("
            CASE estados_reservas.estado
                WHEN 'APROBADA' THEN 1
                WHEN 'PENDIENTE'  THEN 2
                WHEN 'EN CURSO' THEN 3
                WHEN 'FINALIZADA' THEN 4
                WHEN 'CANCELADA' THEN 5
                WHEN 'RECHAZADA' THEN 6
                ELSE 99
            END
        ")->orderBy('reservas.fecha_inicio_reserva');


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
        if(!$reserva){
            return null;
        }
       
        // Se busca el id del estado : CANCELADA
        $estado_cancelado = EstadosReserva::where("estado", "CANCELADA")->value('id');
        $reserva->update(['id_estado_reserva' => $estado_cancelado]);
    }
    

    public function datosParaFormCrear(){
        $id_dependencia = $this->user()->dependencia->id;
        $dependencia = Dependencia::with('hijosRecursivos')->find($id_dependencia);

        $ids = $this->obtenerDependenciasIds($dependencia);

        $vehiculos = Vehiculo::with('estado_vehiculo')->whereIn('id_dependencia_duena', $ids)->get();
        $usuarios = User::with('carnet')->whereIn('id_dependencia', $ids)->get()->each(function ($usuario) {
            $usuario->carnet_vencido = 
                !$usuario->carnet || $usuario->carnet->fecha_vencimiento->isPast();
        })->sortBy('carnet_vencido')
        ->values(); // reindexa
        
         return [
            'vehiculos' => $vehiculos,
            'usuarios'  => $usuarios,
        ];
    }
    public function datosParaFormEditar($id){
        $id_dependencia = $this->user()->dependencia->id;
        $dependencia = Dependencia::with('hijosRecursivos')->find($id_dependencia);
        $reserva = Reserva::findOrFail($id);
        $ids = $this->obtenerDependenciasIds($dependencia);

        $vehiculos = Vehiculo::
        join('estados_vehiculos', 'estados_vehiculos.id', '=', 'vehiculos.id_estado_vehiculo') //GENERAR RELACION CON ESTADO_VEHICULO PARA PODER ORDENAR POR EL CAMPO ESTADO
        ->whereIn('id_dependencia_duena', $ids)
        ->orderByRaw('FIELD(estados_vehiculos.estado, "DISPONIBLE"), ASC')
        ->get(); //Ordenar por disponibles primeros
        $usuarios = User::with('carnet')->whereIn('id_dependencia', $ids)->get()->each(function ($usuario) {
            $usuario->carnet_vencido = 
                !$usuario->carnet || $usuario->carnet->fecha_vencimiento->isPast();
        })->sortBy('carnet_vencido')
        ->values(); // reindexa
        
         return [
            'vehiculos' => $vehiculos,
            'usuarios'  => $usuarios,
            'reserva'   => $reserva,
        ];
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

        $id_dependencia_duena = Vehiculo::where('id', $id_vehiculo)->value('id');
        $id_estado_reserva = EstadosReserva::where("estado", "APROBADA")->value('id');
        $id_dependencia_solicitante = User::where('id', $id_vehiculo)->value('id');
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

    
    function obtenerDependenciasIds($dependencia){
        $ids = [$dependencia->id];

        foreach ($dependencia->dependenciasHijas as $hijo) {
            $ids = array_merge($ids, $this->obtenerDependenciasIds($hijo));
        }
        return $ids;
    }



    public function editarReserva(array $data, $id){
        
    }



    //Datos que se muestran en los inputs de los filtros (los vehiculos que son usados por las dependencias que se muestran y
    // el estado de las reservas)
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


    //Datos que se muestran en los inputs de los filtros (los vehiculos que son usados por las dependencias que se muestran y
    // el estado de las reservas)
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