<?php

namespace App\Services\Reservas;

use App\Models\Dependencia;
use App\Models\EstadosReserva;
use App\Models\Reserva;

use App\Models\Vehiculo;
use App\Services\reservas\BaseReservasServices;


class ReservasExternasService extends BaseReservasServices{


    public function verReservas(){
        $rol = $this->rol();
        $id_dependencia = $this->id_dependencia();
        $query = $this->obtenerDatosVerReservas();
        $ids = $this->obtenerDependenciasIds(Dependencia::find($id_dependencia));

        if($rol == 'Administrador de Dependencia' || $rol == 'Jefe de Area'){
            $query->obtenerDependenciasExternas($id_dependencia);
        }
        else if($rol == 'Operativo'){
            $query->obtenerDependenciasExternas($id_dependencia)->where('id_usuario', $this->user()->id);
        }
       else{
           $query->soloExternas($id_dependencia);
        }

        $total = $query->count();

        $reservas = $query->paginate(10);

        return [
            'reservas' => $reservas,
            'ids' => $ids,
            'total' => $total
        ];
    }



    /**
     * Obtiene los datos necesarios para los formularios
     * de alta y edición de prestamos.
     *
     * Este método centraliza la lógica común del formulario y:
     * - Obtiene la dependencia del usuario logueado junto con todas sus dependencias hijas de forma recursiva (sus id's).
     * - Recupera las consultas base de vehículos y usuarios.
     * - Filtra los vehículos excluyendo aquellos que pertenecen a dependencias propias (evita que en el flujo de préstamos se genere una reserva interna).
     * - Prioriza en el ordenamiento los vehículos habilitados para préstamo.
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
        $id_dependencia = $this->id_dependencia();
        $dependencia = Dependencia::find($id_dependencia);
        $ids = $this->obtenerDependenciasIds($dependencia);
        $base = $this->obtenerDatosBase();
        $rol = $this->rol();

        $queryVehiculos = $base['queryVehiculos'];
        $queryUsuarios  = $base['queryUsuarios'];

        if ($this->rol() !== 'Administrador General') {
            $queryVehiculos->whereNotIn('vehiculos.id_dependencia_duena', $ids);
            $queryUsuarios->whereIn('users.id_dependencia', $ids);
        }

        $vehiculos = $queryVehiculos->get();

        $usuarios = $queryUsuarios->get()
            ->map(function ($usuario) {
                $usuario->carnet_vencido =
                    !$usuario->carnet || $usuario->carnet->fecha_vencimiento->isPast();
                return $usuario;
            })
            ->sortBy('carnet_vencido');


        $arbol = $this->rol() === 'Administrador General'
            ? Dependencia::all()
            : $this->obtenerDependenciasArbol($dependencia);

        return compact('vehiculos', 'usuarios', 'arbol');

    }


    /**
     * Obtiene los datos necesarios para mostrar el formulario de crear de una reserva externa.
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
    public function datosParaFormCrear(){
        $datos = $this->datosForm();
        return [
            'vehiculos' => $datos['vehiculos'],
            'usuarios'  => $datos['usuarios'],
            'dependencias'  => $datos['arbol'],
            'formAction' => route('admin.reservas.externas.crear'),
            'ubicacion' => 'externa',
            'reserva'   => null,
        ];
    }

    /**
     * Obtiene los datos necesarios para mostrar el formulario de edición de una reserva externa.
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
            'formAction' => route('admin.reservas.externas.editar', $id),
            'ubicacion' => 'externa'
        ];
    }


    public function obtenerEstadoReserva(){
        $id_estado_reserva = EstadosReserva::where("estado", "PENDIENTE")->value('id');
        return $id_estado_reserva;
    }


    // True referencia si es prestamo o no (suma una validacion, que el vehiculo este accesible para prestamos)
    public function valoresParametrosValidaciones($id_vehiculo, $fecha_inicio, $fecha_fin, $id_usuario, $id_dependencia_solicitante, $id = null){
        return $this->validaciones($id_vehiculo, $fecha_inicio, $fecha_fin, $id_usuario, $id_dependencia_solicitante, $id, true);
    }




    /**
     * Obtiene los datos necesarios para armar los filtros de la vista de reservas externas.
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
        $id_dependencia = $this->id_dependencia();

        /**
         * Se obtienen únicamente los vehículos que tengan al menos
         * una reserva visible para el usuario según su rol.
         *
         * whereHas:
         * Filtra vehículos que posean reservas que cumplan las condiciones
         */
        $query = Vehiculo::whereHas('reservas', function ($q) use ($rol, $id_dependencia) {

            //Externas de su dependencia
            if($rol == 'Administrador de Dependencia' || $rol == 'Jefe de Area'){
            $q->obtenerDependenciasExternas($id_dependencia)->groupBy('id_vehiculo');
            }

            // ve únicamente las reservas propias de su dependencia que lo involucran
            else if($rol == 'Operativo'){
                $q->obtenerDependenciasExternas($id_dependencia)->where('id_usuario', $this->user()->id)->groupBy('id_vehiculo');
            }
            else{
              $q->soloExternas($id_dependencia)->groupBy('id_vehiculo');

            }
        })

        /**
         * Se cargan las reservas de cada vehículo, aplicando las mismas reglas de visibilidad
         * para que coincidan con el filtro anterior
         */
        ->with(['reservas' => function ($q) use ($rol, $id_dependencia) {

            if($rol == 'Administrador de Dependencia' || $rol == 'Jefe de Area'){
                $q->obtenerDependenciasExternas($id_dependencia)->groupBy('id_vehiculo');
            }

            else if($rol == 'Operativo'){
                 $q->obtenerDependenciasExternas($id_dependencia)->where('id_usuario', $this->user()->id)->groupBy('id_vehiculo');
            }
            else{
                $q->soloExternas($id_dependencia)->groupBy('id_vehiculo');
            }

        }]);

        return [
            'vehiculos_filtros' => $query->orderBy('dominio')->get(),
            'estados_filtros' => EstadosReserva::orderBy('estado')->get(),
        ];
    }

}
