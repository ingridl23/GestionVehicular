<?php

namespace App\Services\reservas;

use App\Contracts\ReservaServiceInterface;
use App\Models\Carnet;
use App\Models\Dependencia;
use App\Models\EstadosReserva;
use App\Models\Reserva;
use App\Models\User;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


abstract class BaseReservasServices implements ReservaServiceInterface{

    public function verReservasPendientes(){
        $id_dependencia = $this->user()->dependencia->id;
        $query = $this->obtenerDatosVerReservas();
        if($this->rol() == "Administrador de Dependencia"){
            $query->obtenerDependenciasExternasPendientes($id_dependencia);
        }
        else if($this->rol() == "Administrador General"){
            $query->soloExternas($id_dependencia)->pendientes();

        }
        else{
             abort(403, 'No tiene permisos para acceder a estas reservas.');
        }

        $reservas = $query->paginate(5);
        return $reservas;
    }

    public function user(){
        return Auth::user();
    }

    public function rol(){
        $rol = $this->user()->getRoleNames();
        return $rol[0] ;
    }

    /**
     * Construye las consultas base para vehículos y usuarios.
     *
     * - La consulta de vehículos:
     *   - Relaciona vehículos con su estado y dependencia dueña
     *   - Ordena priorizando los vehículos en estado "DISPONIBLE"
     *   - Selecciona los datos del vehículo junto al nombre de la dependencia
     *
     * - La consulta de usuarios:
     *   - Incluye la relación con el carnet
     *   - Relaciona usuarios con su dependencia
     *   - Selecciona los datos del usuario junto al nombre de la dependencia
     *
     * Este método no ejecuta las consultas, solo las prepara
     * para que puedan ser reutilizadas o filtradas posteriormente.
     *
     * @return array<string, \Illuminate\Database\Eloquent\Builder>
     */
    protected function obtenerDatosBase(){
        $queryVehiculos = Vehiculo::join('estados_vehiculos', 'estados_vehiculos.id', '=', 'vehiculo.id_estado_vehiculo')
            ->join('dependencias', 'dependencias.id', '=', 'vehiculo.id_dependencia_duena')
            ->orderByRaw("
                CASE
                    WHEN estados_vehiculos.estado = 'DISPONIBLE' THEN 0
                    ELSE 1
                END
            ")
            ->select('vehiculo.*', 'dependencias.nombre');

        $queryUsuarios = User::with('carnet')
            ->join('dependencias', 'dependencias.id', '=', 'users.id_dependencia')
            ->select('users.*', 'dependencias.nombre');

        return compact('queryVehiculos', 'queryUsuarios');
    }

    /**
     * Obtiene de forma recursiva los IDs de una dependencia
     * incluyendo la dependencia principal y todas sus
     * dependencias hijas, nietas, etc.
     *
     * Se utiliza recursividad para recorrer el árbol completo
     * de dependencias sin importar la profundidad.
     *
     * @param  Dependencia $dependencia  Dependencia raíz desde donde iniciar el recorrido
     * @return array<int>                Array con los IDs de todas las dependencias encontradas
     */
    function obtenerDependenciasIds($dependencia){
        $ids = [$dependencia->id];

        foreach ($dependencia->dependenciasHijas as $hijo) {
            $ids = array_merge($ids, $this->obtenerDependenciasIds($hijo));
        }
        return $ids;
    }

    function obtenerDependenciasPadres($dependencia, &$ids = []){
        $ids = [$dependencia->id];


        if ($dependencia->dependenciaPadre) {
            $this->obtenerDependenciasPadres($dependencia->dependenciaPadre, $ids);
        }

        return $ids;
    }

        /**
     * Obtiene de forma recursiva las dependencias pertenecientes al arbol de la dependencia padre
     *
     * Se utiliza recursividad para recorrer el árbol completo
     * de dependencias sin importar la profundidad.
     *
     * @param  Dependencia $dependencia  Dependencia raíz desde donde iniciar el recorrido
     * @return array<Dependencia>                Array con las dependencias encontradas
     */
    function obtenerDependenciasArbol($dependencia){

        $dependencias = [$dependencia];

        foreach ($dependencia->dependenciasHijas as $hijo) {
            $dependencias = array_merge($dependencias, $this->obtenerDependenciasArbol($hijo));
        }
        return $dependencias;
    }


    /**
     * Construye la consulta base para visualizar reservas.
     *
     * - Carga de forma anticipada las relaciones necesarias:
     *   estado de la reserva, vehículo asociado, usuario designado y
     *   dependencia solicitante.
     *
     * - Ordena las reservas según una prioridad definida por el estado:
     *   1. APROBADA
     *   2. PENDIENTE
     *   3. EN CURSO
     *   4. FINALIZADA
     *   5. CANCELADA
     *   6. RECHAZADA
     *   Cualquier otro estado queda al final.
     *
     * - Como criterio secundario, ordena por fecha de inicio de la reserva.
     *
     * Este método no ejecuta la consulta, solo devuelve el builder para
     * permitir aplicar filtros adicionales antes de obtener los resultados.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */

    protected function obtenerDatosVerReservas(){

        $query = Reserva::with('estado_reserva', 'vehiculo', 'usuario', 'dependencia_solicitante')
         ->orderByRaw("
            CASE (
            SELECT estado
            FROM estados_reservas
            WHERE estados_reservas.id = reserva.id_estado_reserva
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
        return($query);
    }

    /**
     * Obtiene la reserva según el $id que se pasa por parametro.
     * Incluye la informacion de la reserva, el vehiculo vinculado
     * y el usuario designado al manejo.
     *
     *
     * @param  Int $id  ID de la dependencia a buscar
     * @return array<int>                Array con los IDs de todas las dependencias encontradas
     */

    public function verReserva($id){
        $reserva = Reserva::with('estado_reserva', 'vehiculo.estadoNafta', 'usuario.carnet', 'dependencia_solicitante.direccion', 'dependencia_duena.direccion')
        ->find($id);
        $vtvVigente = Vehiculo::vtv_vigente($reserva->vehiculo->id);
        $carnetVigente = Carnet::carnetVigente($reserva->usuario->id);
        return [
            'reserva' => $reserva,
            'vtv'  => $vtvVigente,
            'carnet_vigente' => $carnetVigente,
        ];
        return null;
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


    public function crearReserva($request){
        $fecha_inicio = Carbon::createFromFormat('Y-m-d\TH:i', $request->input('fecha_inicio'));
        $fecha_fin = Carbon::createFromFormat('Y-m-d\TH:i', $request->input('fecha_fin'));
        $id_vehiculo = $request->id_vehiculo;
        $id_usuario = $request->id_usuario;
        $id_dependencia_solicitante = $request->id_dependencia;

       $validaciones = $this->valoresParametrosValidaciones($id_vehiculo, $fecha_inicio, $fecha_fin, $id_usuario, $id_dependencia_solicitante, null);
        if($validaciones != null){
            return $validaciones;
        }

        $id_dependencia_duena = Vehiculo::where('id', $id_vehiculo)->value('id_dependencia_duena');
        $id_estado_reserva = $this->obtenerEstadoReserva();

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



    public function editarReserva($request, $id){
        $fecha_inicio = Carbon::createFromFormat('Y-m-d\TH:i', $request->input('fecha_inicio'));
        $fecha_fin = Carbon::createFromFormat('Y-m-d\TH:i', $request->input('fecha_fin'));
        $id_vehiculo = $request->id_vehiculo;
        $id_usuario = $request->id_usuario;
        $reserva = Reserva::findOrFail($id);
         $id_dependencia_solicitante = $request->id_dependencia;



        // Se redirige a cada service (interna o externa) para ver que valores de los parametros
        //debe tomar
        $validaciones = $this->valoresParametrosValidaciones($id_vehiculo, $fecha_inicio, $fecha_fin, $id_usuario, $id_dependencia_solicitante, $id);

        if($validaciones != null){
            return $validaciones;
        }



        $id_dependencia_duena = Vehiculo::where('id', $id_vehiculo)->value('id_dependencia_duena');
        $id_estado_reserva = $this->obtenerEstadoReserva();


        //editar reserva
        $reserva->update([
            'fecha_inicio_reserva' => $fecha_inicio,
            'fecha_fin_reserva'    => $fecha_fin,
            'id_vehiculo'          => $id_vehiculo,
            'id_estado_reserva'    => $id_estado_reserva,
            'id_usuario'           => $id_usuario,
            'id_dependencia_duena' => $id_dependencia_duena,
            'id_dependencia_solicitante' => $id_dependencia_solicitante,
        ]);

    }

    public function editarConductor($request, $id){
        $reserva = Reserva::findOrFail($id);
        $fecha_inicio = $reserva->fecha_inicio_reserva;
        $fecha_fin = $reserva->fecha_fin_reserva;
        $id_vehiculo = $reserva->id_vehiculo;

        $id_usuario = $request->id_usuario;

        $id_dependencia_solicitante = $reserva->id_dependencia_solicitante;


        $validaciones = $this->valoresParametrosValidaciones($id_vehiculo, $fecha_inicio, $fecha_fin, $id_usuario, $id_dependencia_solicitante, $id);

        if($validaciones != null){
            return $validaciones;
        }

        //editar reserva
        $reserva->update([
            'id_usuario' => $id_usuario,
        ]);
    }


    /**
     * Ejecuta todas las validaciones necesarias para crear o editar
     * una reserva o préstamo de vehículo.
     *
     * Las validaciones incluyen:
     * - Disponibilidad del vehículo en el rango de fechas solicitado
     * - Estado del vehículo (habilitado / disponible)
     * - Habilitación del vehículo para préstamos (si corresponde)
     * - Disponibilidad del usuario en el mismo rango de fechas
     * - Habilitación del usuario (carnet vigente)
     *
     * El método retorna el primer error detectado y finaliza,
     * o null si todas las validaciones son correctas.
     *
     * @param int        $id_vehiculo   ID del vehículo a reservar
     * @param \DateTime  $fecha_inicio  Fecha y hora de inicio de la reserva
     * @param \DateTime  $fecha_fin     Fecha y hora de fin de la reserva
     * @param int        $id_usuario    ID del usuario asignado
     * @param int|null   $id            ID de la reserva (solo para cuando se edita (sino sucede que compara conmigo))
     * @param bool       $esPrestamo    Indica si la operación es un préstamo
     *
     * @return array|null               Código de error o null si es válido
     */

    public function validaciones($id_vehiculo, $fecha_inicio, $fecha_fin, $id_usuario, $id_dependencia_solicitante, $id = null, $esPrestamo = false) {

    // ===============================
    // VEHÍCULO OCUPADO QUE TENGA LA RESERVA APROBADA/EN CURSO/PENDIENTE
    // ===============================
    $vehiculoQuery = Reserva::join('estados_reservas', 'estados_reservas.id', '=', 'reserva.id_estado_reserva')
        ->where('id_vehiculo', $id_vehiculo)
        ->whereIn('estados_reservas.estado', ['APROBADA', 'EN CURSO', 'PENDIENTE'])
        ->where(function ($q) use ($fecha_inicio, $fecha_fin) {
            $q->where('fecha_inicio_reserva', '<', $fecha_fin)
              ->where('fecha_fin_reserva', '>', $fecha_inicio);
        });

    if ($id != null) {
        $vehiculoQuery->where('reserva.id', '!=', $id);
    }

    if ($vehiculoQuery->exists()) {
        return ['vehiculo', true];
    }

    // ===============================
    // VEHÍCULO HABILITADO
    // ===============================
    $vehiculoQuery = Vehiculo::where('id', $id_vehiculo)
        ->whereHas('estadoVehiculo', function ($q) {
            $q->where('estado', 'DISPONIBLE');
        });

    // ===============================
    // VEHÍCULO HABILITADO PARA PRESTAMO
    // ===============================
    if ($esPrestamo) {
        $vehiculoQuery->where('habilitado_prestamo', true);
    }

    if (!$vehiculoQuery->exists()) {
        return ['vehiculo_no_habilitado', true];
    }

    // ===============================
    // USUARIO OCUPADO
    // ===============================
    $usuarioQuery = Reserva::join('estados_reservas', 'estados_reservas.id', '=', 'reserva.id_estado_reserva')
        ->where('id_usuario', $id_usuario)
        ->whereIn('estados_reservas.estado', ['APROBADA', 'EN CURSO', 'PENDIENTE'])
        ->where(function ($q) use ($fecha_inicio, $fecha_fin) {
            $q->where('fecha_inicio_reserva', '<', $fecha_fin)
              ->where('fecha_fin_reserva', '>', $fecha_inicio);
        });


    if ($id) {
        $usuarioQuery->where('reserva.id', '!=', $id);
    }

    if ($usuarioQuery->exists()) {
        return ['usuario', true];
    }

    // ===============================
    // USUARIO HABILITADO
    // ===============================
    if (!Carnet::carnetVigente($id_usuario)) {
        return ['usuario_no_habilitado', true];
    }

    // ===============================
    // DEPENDENCIA VALIDA
    // ===============================

    $id_dependencia = User::where('id', $id_usuario)->value('id_dependencia');
    $dependencia = Dependencia::findOrFail($id_dependencia);
    $idsPermitidos = $this->obtenerDependenciasIds($dependencia);

    if($dependencia->dependenciaPadre){
        // Permite tener todos los id's en caso de ser la dependencia mas lejos de la raíz del arbol de dependencias
        $idsPermitidos = array_merge($idsPermitidos, $this->obtenerDependenciasPadres($dependencia));

        $idsPermitidos = array_merge($this->obtenerDependenciasIds($dependencia->dependenciaPadre));
    }


    // Verifica que, si no es un préstamo, la dependencia solicitante se encuentre en el arbol que le corresponde
    if(!in_array($id_dependencia_solicitante, $idsPermitidos)){
        return ['dependencia', true];
    }

     // Verifica que al ser un prestamo, la dependencia solicitante no se encuentre en el arbol que le corresponde
     // Este caso se ve más en el administrador general ya que al crear o editar se le muestran todos los datos cargados en la base de datos
    if($esPrestamo){
        if(in_array($id_vehiculo, $idsPermitidos)){
            return ['dependencia_prestamo', true];
        }
    }



    return null;
    }


    public function autorizarPrestamo($id){
        $reserva = Reserva::findOrFail($id);

        $fecha_inicio = $reserva->fecha_inicio_reserva;
        $fecha_fin =$reserva->fecha_fin_reserva;
        $id_vehiculo = $reserva->id_vehiculo;
        $id_usuario = $reserva->id_usuario;

        $id_dependencia_solicitante = $reserva->id_dependencia_solicitante;


        $validaciones = $this->valoresParametrosValidaciones($id_vehiculo, $fecha_inicio, $fecha_fin, $id_usuario, $id_dependencia_solicitante, $reserva->id);

        if($validaciones != null){
            return $validaciones;
        }

        $id_estado_reserva = EstadosReserva::where("estado", "APROBADA")->value('id');
        return $reserva->update(['id_estado_reserva' => $id_estado_reserva]);


    }


    public function rechazarPrestamo($id){
        $reserva = Reserva::findOrFail($id);
        $id_estado_reserva = EstadosReserva::where("estado", "RECHAZADA")->value('id');
        return $reserva->update(['id_estado_reserva' => $id_estado_reserva]);
    }

    //Estado que tomará la reserva cuando se cree o se edite
    // Si es interna -> APROBADA (automaticamente)
    // Si es externa -> PENDIENTE hasta que alguien la autorice.
    abstract public function obtenerEstadoReserva();



    protected function valoresParametrosValidaciones($id_vehiculo, $fecha_inicio, $fecha_fin, $id_usuario, $id_dependencia_solicitante, $id = null) {
        return $this->validaciones($id_vehiculo, $fecha_inicio, $fecha_fin, $id_usuario, $id_dependencia_solicitante, $id, false);
    }
}
