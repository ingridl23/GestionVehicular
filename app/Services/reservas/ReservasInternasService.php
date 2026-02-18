<?php

namespace App\Services\Reservas;

use App\Models\Dependencia;
use App\Models\EstadosReserva;
use App\Models\Reserva;
use App\Models\Vehiculo;


class ReservasInternasService extends BaseReservasServices{


    public function verReservas(){
        $rol = $this->rol();
        $id_dependencia = $this->user()->dependencia->id;

       $query = $this->obtenerDatosVerReservas();

        if($rol == 'Administrador de Dependencia' || $rol == 'Jefe de Area'){
            $query->obtenerDependenciasInternas($id_dependencia);
        }

        else if($rol == 'Operativo'){
           $query->soloInternas($id_dependencia);

        }
       else{
    $query->soloInternas($id_dependencia);
}


        $reservas = $query->paginate(5);
        return $reservas;
    }


    /**
     * Obtiene los datos necesarios para los formularios de alta y edición de reservas internas.
     *
     * Este método centraliza la lógica común del formulario y:
     * - Obtiene la dependencia del usuario logueado junto con todas sus dependencias hijas de forma recursiva (sus id's).
     * - Recupera las consultas base de vehículos y usuarios.
     * - Filtra los vehículos permitiendo unicamente aquellos que pertenecen
     *    a dependencias propias (evita que en el flujo de reservas internas se genere un prestamo).
     * - Obtiene los usuarios y marca aquellos que tienen el carnet vencido para facilitar su visualización y validación en el formulario.
     *
     * Este método es reutilizado por los distintos flujos (crear / editar),
     * evitando duplicación de consultas y reglas de negocio.
     *
     * @return array{
     *     vehiculos: \Illuminate\Support\Collection,
     *     usuarios: \Illuminate\Support\Collection
     * }
     */
    private function datosForm(){
        $id_dependencia = $this->user()->dependencia->id;
        $dependencia = Dependencia::with('dependenciasHijas')->find($id_dependencia);
        $arbol = $this->obtenerDependenciasArbol($dependencia);

        $ids = $this->obtenerDependenciasIds($dependencia);

        $base = $this->obtenerDatosBase();

        $vehiculos = $base['queryVehiculos']
            ->whereIn('vehiculos.id_dependencia_duena', $ids)
            ->get();

        $usuarios = $base['queryUsuarios']
            ->whereIn('users.id_dependencia', $ids)
            ->get()
            ->map(function ($usuario) {
            $usuario->carnet_vencido =
                !$usuario->carnet || $usuario->carnet->fecha_vencimiento->isPast();
            return $usuario;
        })->sortBy('carnet_vencido');
        return compact('vehiculos', 'usuarios', 'arbol');
    }


    /**
     * Obtiene los datos necesarios para mostrar el formulario de crear de una reserva interna.
     *
     * Este método:
     * - Delegar en datosForm() la obtención de los vehículos y usuarios disponibles.
     * - Prepara la información base necesaria para renderizar el formulario,
     *   incluyendo la reserva, la acción del formulario y la ubicación del flujo.
     *
     * La lógica de consultas, filtros y ordenamiento de vehículos y usuarios
     * se centraliza en el método datosForm() para evitar duplicación de código
     * entre los distintos flujos (alta / edición).
     *
     * @param int $id ID de la reserva a editar
     * @return array
     */
    public function datosParaFormCrear(){
        $datos = $this->datosForm();

        return [
            'vehiculos' => $datos['vehiculos'],
            'usuarios'  => $datos['usuarios'],
            'dependencias'  => $datos['arbol'],
            'formAction' => route('admin.reservas.internas.crear'),
            'reserva'   => null,
            'ubicacion'   => null,
        ];
    }

    /**
     * Obtiene los datos necesarios para mostrar el formulario de edición de una reserva interna.
     *
     * Este método:
     * - Recupera la reserva que se va a editar.
     * - Delegar en datosForm() la obtención de los vehículos y usuarios disponibles.
     * - Prepara la información base necesaria para renderizar el formulario,
     *   incluyendo la reserva, la acción del formulario y la ubicación del flujo.
     *
     * La lógica de consultas, filtros y ordenamiento de vehículos y usuarios
     * se centraliza en el método datosForm() para evitar duplicación de código
     * entre los distintos flujos (alta / edición).
     *
     * @param int $id ID de la reserva a editar
     * @return array
     */
    public function datosParaFormEditar($id){
        $reserva = Reserva::findOrFail($id);
        $datos = $this->datosForm();

        return [
            'vehiculos' => $datos['vehiculos'],
            'usuarios'  => $datos['usuarios'],
            'dependencias'  => $datos['arbol'],
            'reserva'   => $reserva,
            'formAction' => route('admin.reservas.internas.editar', $id),
            'ubicacion'   => null,
        ];
    }



    public function obtenerEstadoReserva(){
        $id_estado_reserva = EstadosReserva::where("estado", "APROBADA")->value('id');
        return $id_estado_reserva;
    }


    public function valoresParametrosValidaciones($id_vehiculo, $fecha_inicio, $fecha_fin, $id_usuario, $id_dependencia_solicitante, $id = null){
        $resultado = $this->validaciones($id_vehiculo, $fecha_inicio, $fecha_fin, $id_usuario, $id_dependencia_solicitante, $id);
        return $resultado;
    }




    /**
     * Obtiene los datos necesarios para armar los filtros de la vista de reservas internas.
     *
     * Este método:
     * - Determina qué vehículos deben mostrarse en el filtro según las reservas visibles para el usuario logueado (se determina por rol).
     * - Aplica reglas de visibilidad basadas en el rol del usuario (Administrador de Dependencia, Jefe de Área, Operativo u otros).
     * - Filtra los vehículos para que solo se incluyan aquellos que tengan al menos una reserva accesible para el usuario o la dependencia (depende del rol).
     * - Carga únicamente los vehículos que cuentan con una reserva evitando la duplicacion de elementos, manteniendo consistencia entre el filtro y los datos cargados.
     * - Devuelve también el listado de estados posibles de las reservas.
     *
     * El filtro es el mismo para todos los roles; lo que cambia es
     * qué información se muestra según los permisos del usuario.
     *
     * @return array
     */
    public function datosFiltros(){
        $rol = $this->rol();
        $id_dependencia = $this->user()->dependencia->id;
        $id_usuario = $this->user()->id;

        //whereHas -> vehiculos que tengan al menos una reserva que cumpla con las condiciones (FILTRA)
        $query = Vehiculo::whereHas('reservas', function ($q) use ($rol, $id_dependencia, $id_usuario) {

            //Internas de su dependencia
            if ($rol === 'Administrador de Dependencia' || $rol === 'Jefe de Area') {
                $q->obtenerDependenciasInternas($id_dependencia);
            }

            // Sus reservas
            elseif ($rol === 'Operativo') {
                $q->obtenerDependenciasInternas($id_dependencia)->where('id_usuario', $id_usuario);
            }

            // Todas las reservas que sean internas
            else {
                $q->soloInternas($id_dependencia);

            }

        })

        //Carga las reservas de cada vehiculo pero que cumplan con las condiciones
        ->with(['reservas' => function ($q) use ($rol, $id_dependencia, $id_usuario) {

            if ($rol === 'Administrador de Dependencia' || $rol === 'Jefe de Area') {
                $q->obtenerDependenciasInternas($id_dependencia);
            }
            elseif ($rol === 'Operativo') {
                $q->obtenerDependenciasInternas($id_dependencia)
                ->where('id_usuario', $id_usuario);
            }
            else {
           $q->soloInternas($id_dependencia);

            }

        }]);

        return [
            'vehiculos_filtros' => $query->orderBy('dominio')->get(),
            'estados_filtros' => EstadosReserva::orderBy('estado')->get(),
        ];
    }



}
