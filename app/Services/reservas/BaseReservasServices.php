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


    public function user(){
        return Auth::user();
    }

    public function rol(){
        $rol = $this->user()->getRoleNames();
        return $rol[0] ;
    }

    protected function id_dependencia(){
        return $this->user()->dependencia->id;
    }


    /**
     * Obtiene las reservas externas pendientes según el rol del usuario autenticado.
     *
     * - Administrador de Dependencia:
     *   Visualiza las reservas externas pendientes asociadas al arbol jerarquico de la dependencia del usuario.
     *
     *
     * - Administrador General:
     *   Visualiza todas las reservas externas pendientes del sistema.
     *
     * - Otros roles:
     *   No tienen permiso para acceder a esta información (retorna 403).
     *
     * La consulta se construye a partir de obtenerDatosVerReservas(),
     * luego se calcula el total de registros y se retornan los resultados
     * paginados (10 por página).
     *
     * @return array{
     *     reservas: \Illuminate\Contracts\Pagination\LengthAwarePaginator,
     *     total: int
     * }
     */
    public function verReservasPendientes(){
        $id_dependencia = $this->id_dependencia();
        $query = $this->obtenerDatosVerReservas();

        if($this->rol() == "Administrador de Dependencia"){
            $query->obtenerDependenciasExternas($id_dependencia, false)->pendientes();
        }
        else if($this->rol() == "Administrador General"){
            $query->soloExternas($id_dependencia)->pendientes();

        }
        else{
            abort(403, 'No tiene permisos para acceder a estas reservas.');
        }

        $total = $query->count();
        $reservas = $query->paginate(10);

        return [
            'reservas' => $reservas,
            'total' => $total
        ];
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
        $queryVehiculos = Vehiculo::join('estados_vehiculos', 'estados_vehiculos.id', '=', 'vehiculos.id_estado_vehiculo')
            ->join('dependencias', 'dependencias.id', '=', 'vehiculos.id_dependencia_duena')
            ->orderByRaw("
                CASE
                    WHEN estados_vehiculos.estado = 'DISPONIBLE' THEN 0
                    ELSE 1
                END
            ")
            ->select('vehiculos.*', 'dependencias.nombre');

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
     * @return array<Dependencia> Array con las dependencias encontradas
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
        return($query);
    }

    /**
     * Obtiene la reserva según el $id que se pasa por parametro.
     * Incluye la informacion de la reserva, el vehiculo vinculado
     * y el usuario designado al manejo.
     *
     *
     * @param  Int $id  ID de la dependencia a buscar
     * @return array<int> Array con los IDs de todas las dependencias encontradas
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
        $vehiculoQuery = Reserva::join('estados_reservas', 'estados_reservas.id', '=', 'reservas.id_estado_reserva')
            ->where('id_vehiculo', $id_vehiculo)
            ->whereIn('estados_reservas.estado', ['APROBADA', 'EN CURSO', 'PENDIENTE'])
            ->where(function ($q) use ($fecha_inicio, $fecha_fin) {
                $q->where('fecha_inicio_reserva', '<', $fecha_fin)
                ->where('fecha_fin_reserva', '>', $fecha_inicio);
            });

        if ($id != null) {
            $vehiculoQuery->where('reservas.id', '!=', $id);
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
        $usuarioQuery = Reserva::join('estados_reservas', 'estados_reservas.id', '=', 'reservas.id_estado_reserva')
            ->where('id_usuario', $id_usuario)
            ->whereIn('estados_reservas.estado', ['APROBADA', 'EN CURSO', 'PENDIENTE'])
            ->where(function ($q) use ($fecha_inicio, $fecha_fin) {
                $q->where('fecha_inicio_reserva', '<', $fecha_fin)
                ->where('fecha_fin_reserva', '>', $fecha_inicio);
            });


        if ($id) {
            $usuarioQuery->where('reservas.id', '!=', $id);
        }

        if ($usuarioQuery->exists()) {
            return ['usuario', true];
        }

        // ===============================
        // USUARIO HABILITADO Y CARNET CUBRE TODO EL PERÍODO
        // ===============================

        $usuario = User::with('carnet')->find($id_usuario);

        if (!$usuario || !$usuario->carnet) {
            return ['usuario_sin_carnet', true];
        }

        $fechaVencimiento = $usuario->carnet->fecha_vencimiento;

        // Si vence antes de terminar la reserva
        if ($fechaVencimiento->lt($fecha_fin)) {
            return ['carnet_no_cubre_periodo', true];
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
            $vehiculo_id_dependencia = Vehiculo::where("id", $id_vehiculo)->value("id_dependencia_duena");
            if(in_array($vehiculo_id_dependencia, $idsPermitidos)){
                return ['dependencia_prestamo', true];
            }
        }

        return null;
    }

    /**
     * Autoriza un préstamo (reserva externa) si cumple con todas las validaciones correspondientes.
     *
     * Validaciones realizadas:
     *  - Verifica que el usuario tenga carnet asociado.
     *  - Verifica que el carnet cubra todo el período de la reserva.
     *  - Ejecuta validaciones adicionales (vehiculo disponible en fecha , vehiculo disponible y habilitado para prestamo, usuario disponible).
     *
     * Si alguna validación falla, retorna un arreglo indicando el tipo de error.
     * Si todas las validaciones son correctas, actualiza el estado de la reserva
     * a "APROBADA".
     *
     * @param int $id ID de la reserva a autorizar.
     * @return bool|array True si se actualiza correctamente o array con error.
     */
    public function autorizarPrestamo($id){
        $reserva = Reserva::findOrFail($id);

        $usuario = $reserva->usuario()->with('carnet')->first();

        if (!$usuario->carnet) {
              return ['usuario_sin_carnet', true];
            }

        $fechaVencimiento = $usuario->carnet->fecha_vencimiento;

       if ($fechaVencimiento->lt($reserva->fecha_fin)) {
              return ['carnet_no_cubre_periodo', true];
         }

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




    /**
    * Prepara y delega los parámetros necesarios para ejecutar las validaciones
    * de negocio de una reserva.
    *
    * Este método actúa como intermediario hacia el método validaciones(),
    * enviando los valores específicos que cada clase hija necesita evaluar.
    *
    * Se encuentra definido en las clases hijas, donde cada una puede
    * interpretar o utilizar los parámetros de manera diferente según
    * el tipo de reserva (por ejemplo, interna o préstamo).
    *
    * @param int $id_vehiculo
    * @param \Carbon\Carbon|string $fecha_inicio
    * @param \Carbon\Carbon|string $fecha_fin
    * @param int $id_usuario
    * @param int $id_dependencia_solicitante
    * @param int|null $id ID de la reserva (opcional, útil en ediciones)
    * @return mixed Resultado de las validaciones de negocio.
    */

    protected function valoresParametrosValidaciones($id_vehiculo, $fecha_inicio, $fecha_fin, $id_usuario, $id_dependencia_solicitante, $id = null) {
        return $this->validaciones($id_vehiculo, $fecha_inicio, $fecha_fin, $id_usuario, $id_dependencia_solicitante, $id, false);
    }


    /**
     * Obtiene y retorna las reservas correspondientes a préstamos externos (donde yo soy el dueño del vehiculo y me lo solicita otra dependencia).
     *
     * - Administrador de Dependencia y Jefe de Área:
     *   Visualizan los préstamos externos vinculados a su dependencia,
     *   filtrando por las dependencias que pertenecen a su árbol jerárquico.
     *
     *
     * - Si es otro rol:
     *   Devuelve null a no tener los permisos correspondientes.
     *
     * La respuesta se retorna en formato JSON con el listado de reservas si se genero efectivamente.
     * La respuesta se retorna en formato JSON con el valor null al no cumplir con los requisitos.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verPrestamosExternos(){
        $rol = $this->rol();
        $id_dependencia = $this->id_dependencia();
        $query = $this->obtenerDatosVerReservas();

        $ids = $this->obtenerDependenciasIds(Dependencia::find($id_dependencia));


        if($rol == 'Administrador de Dependencia' || $rol == 'Jefe de Area'){
            $query->obtenerDependenciasExternas($id_dependencia)->whereIn('id_dependencia_duena' , $ids);
        }
       else{
           $query->soloExternas($id_dependencia)->where('id_dependencia_duena' , $ids);
        }

        return response()->json(['reservas' => $query->get()]);

    }

    public function verPrestamosInternos(){
        $rol = $this->rol();
        $id_dependencia = $this->id_dependencia();
        $query = $this->obtenerDatosVerReservas();
        $ids = $this->obtenerDependenciasIds(Dependencia::find($id_dependencia));

        if($rol == 'Administrador de Dependencia' || $rol == 'Jefe de Area'){
            $query->obtenerDependenciasExternas($id_dependencia)->whereIn('id_dependencia_solicitante' , $ids);
        }

       else{
           return null;
        }


       return $query->paginate(3);

    }
}
