<?php

namespace App\Http\Controllers;
use App\Models\Dependencia;
use Illuminate\Http\Request;
use App\Http\Requests\DependenciaRequest;
use Illuminate\Validation\ValidationException;
use App\Services\DependenciaService;
use Illuminate\Support\Facades\Auth;
/**
 * @class DependenciaController
 *
 * @brief Controlador encargado de la gestión integral de las dependencias
 * del sistema de Gestión Vehicular del distrito de Tres Arroyos.
 *
 * Este controlador actúa como capa de orquestación entre:
 * - La capa de presentación (views)
 * - La capa de negocio (DependenciaService)
 * - La capa de autorización (Policies)
 *
 * Responsabilidades principales:
 * - Listado general y detalle de dependencias
 * - Alta, baja y modificación de dependencias
 * - Cambio de estado activa/inactiva
 * - Filtros dinámicos con paginación
 * - Control de acceso basado en roles
 *
 * Reglas de negocio relevantes:
 * - Una dependencia puede tener jerarquía (dependencia padre).
 * - Puede estar activa o inactiva.
 * - La visibilidad depende del rol del usuario autenticado.
 *
 * @package App\Http\Controllers
 */
class DependenciaController extends Controller{

    protected DependenciaService $service;

    /**
 * Constructor del controlador.
 *
 * Inyecta la capa de servicio encargada de la lógica
 * de negocio relacionada a la entidad Dependencia.
 *
 * @param DependenciaService $service Servicio de gestión de dependencias.
 */

    public function __construct(DependenciaService $service)
    {
        $this->service = $service;
    }



/**
 * Mostrar listado general de dependencias.
 *
 * Aplica autorización mediante Policy (vistaGeneral)
 * y obtiene los datos desde la capa de servicio.
 *
 * Incluye:
 * - Listado paginado
 * - Total de registros
 * - Datos auxiliares para filtros
 *
 * @return \Illuminate\View\View Vista principal de dependencias.
 *
 * @throws \Illuminate\Auth\Access\AuthorizationException
 *         Si el usuario no posee permiso para visualizar.
 // permission = ver dependencias
 */

    public function verDependencias(){
        $this->authorize('vistaGeneral', Dependencia::class);
        $datos = $this->service->verDependencias();
         $data = array_merge(
            ['dependencias' => $datos['dependencias']],
            ['total' => $datos['total']],
            $this->service->datosFiltros()
        );
        return view('ui.dependencias.dependencias', $data);
    }



    /**
 * Mostrar detalle de una dependencia específica.
 *
 * Valida la existencia de la dependencia y aplica
 * autorización individual mediante Policy (view).
 *
 * @param int $id Identificador de la dependencia.
 *
 * @return \Illuminate\View\View Vista de detalle.
 *
 * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
 *         Si la dependencia no existe.
 *
 * @throws \Illuminate\Auth\Access\AuthorizationException
 *         Si el usuario no posee permisos.
// permission = ver dependencias
 */

    public function verDependencia($id){
        $dependencia = $this->service->verDependencia($id);
        $dependencia_autorizar = Dependencia::findOrFail($id);

        $this->authorize('view', $dependencia_autorizar);

        return view('ui.dependencias.dependencia', $dependencia);
    }


/**
 * Eliminar una dependencia del sistema.
 *
 * La operación es gestionada por la capa de servicio.
 * En caso de restricciones de negocio, se captura
 * ValidationException y se devuelve respuesta JSON estructurada.
 *
 * Posibles restricciones:
 * - Dependencia jerárquica superior.
 * - Dependencia con relaciones activas.
 *
 * @param int $id Identificador de la dependencia.
 *
 * @return \Illuminate\Http\JsonResponse Resultado de la operación.
 *
 * @throws \Illuminate\Validation\ValidationException
 */

    // permiso = eliminar dependencias
    public function eliminarDependencia($id){
            //$dependencia = $this->service->verDependencia($id);
            //$this->authorize('delete', $dependencia);

            try {
                $eliminada = $this->service->eliminarDependencia($id);

                return response()->json([
                    'success' => $eliminada
                ]);
            } catch (ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
    }


/**
 * Cambiar el estado activo/inactivo de una dependencia.
 *
 * Valida que el valor recibido sea booleano y delega
 * la actualización al servicio correspondiente.
 *
 * Reglas de negocio:
 * - Solo acepta valores booleanos.
 * - No elimina registros, solo alterna estado.
 *
 * @param int $id Identificador de la dependencia.
 * @param \Illuminate\Http\Request $request Request con campo 'activa'.
 *
 * @return mixed Resultado de la actualización.
 *
 * @throws \Illuminate\Validation\ValidationException
// permiso = ver dependencias
// Actualiza el estado de la dependencia, alternando entre activa (1) e inactiva (0) según su estado actual.
 */

    public function cambiarActivaDependencia($id, Request $request){

        $request->validate([
            'activa' => 'required|boolean',

        ]);
        //$dependencia = $this->service->verDependencia($id);
        //$this->authorize('toggle', $dependencia);

        return $this->service->cambiarActivaDependencia($id, $request);
    }



/**
 * Obtener datos necesarios para renderizar el formulario de creación.
 *
 * Si se especifica una dependencia padre, se valida su existencia.
 * Se aplica autorización dinámica basada en la jerarquía.
 *
 * @param \Illuminate\Http\Request $request Request con posible id_dependencia_padre.
 *
 * @return \Illuminate\View\View Vista del formulario de creación.
 *
 * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
 * @throws \Illuminate\Auth\Access\AuthorizationException
// permiso = crear dependencias
// datosRelacionDependencia = Recupera la información de las tablas relacionadas a la entidad Dependencia
 */
  public function datosParaCrearDependencia(Request $request){
    $dependenciaPadre = null;

    if ($request->filled('id_dependencia_padre')) {
        $dependenciaPadre = Dependencia::findOrFail(
            $request->id_dependencia_padre
        );
    }

    $this->authorize('create', [Dependencia::class, $dependenciaPadre]);

    return view(
        'ui.dependencias.formularios.crear',
        $this->service->datosRelacionesDependencia()
    );
}


/**
 * Crear una nueva dependencia en el sistema.
 *
 * Utiliza DependenciaRequest para validación estructurada.
 * Aplica autorización considerando posible jerarquía.
 *
 * Reglas de negocio:
 * - No se permiten dependencias duplicadas.
 * - Puede establecer dependencia padre.
 * - Se registra como activa por defecto (según implementación).
 *
 * @param \App\Http\Requests\DependenciaRequest $request Datos validados.
 *
 * @return \Illuminate\Http\RedirectResponse Redirección al listado.
 *
 * @throws \Illuminate\Auth\Access\AuthorizationException
// permission = crear dependencias
 */
  public function crearDependencia(DependenciaRequest $request)
{
    $dependenciaPadre = null;

    if ($request->filled('id_dependencia_padre')) {
        $dependenciaPadre = Dependencia::findOrFail(
            $request->id_dependencia_padre
        );
    }

    $this->authorize('create', [Dependencia::class, $dependenciaPadre]);

    $this->service->crearDependencia($request->validated());

    return redirect()
        ->route('dependencias.index')
        ->with('success', 'La dependencia fue creada correctamente.');
}

/**
 * Obtener datos para el formulario de edición de una dependencia.
 *
 * Recupera la información actual y las relaciones necesarias
 * para completar el formulario.
 *
 * @param int $id Identificador de la dependencia.
 *
 * @return \Illuminate\View\View Vista del formulario de edición.
 *
 * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
  // permission = editar dependencias
 */
   public function datosParaEditarDependencia($id){

    $dependencia = $this->service->verDependencia($id);

    //$this->authorize('update', $dependencia);

    return view(
        'ui.dependencias.formularios.editar',
        $this->service->datosRelacionesDependencia($id)
    );
}


/**
 * Actualizar una dependencia existente.
 *
 * Aplica validaciones mediante DependenciaRequest
 * y delega la lógica de actualización al servicio.
 *
 * Reglas de negocio:
 * - Mantiene coherencia jerárquica.
 * - Actualiza dirección y relaciones asociadas.
 *
 * @param \App\Http\Requests\DependenciaRequest $request Datos validados.
 * @param int $id Identificador de la dependencia.
 *
 * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de éxito.
 */

    // permiso = editar dependencias
   public function editarDependencia(DependenciaRequest $request,$id) {
    $dependencia = $this->service->verDependencia($id);

    //$this->authorize('update', $dependencia);

    $this->service->editarDependencia(
        $request->validated(),
        $id
    );

    return redirect()
        ->route('dependencias.index')
        ->with('success', 'La dependencia fue actualizada correctamente.');
}

/**
 * Filtrar dependencias dinámicamente según criterios enviados por request.
 *
 * Filtros soportados:
 * - Nombre
 * - Dependencia padre
 * - Estado activa/inactiva
 * - Calle (relación Dirección)
 * - Ciudad (relación Dirección)
 *
 * Comportamiento según rol:
 * - Administrador de Dependencia: solo dependencias internas.
 * - Jefe de Área: solo su dependencia.
 *
 * Seguridad:
 * - Validación de campos permitidos para ordenamiento.
 * - Protección contra inyección SQL en orderBy.
 *
 * Incluye:
 * - Ordenamiento dinámico controlado.
 * - Priorización de dependencia padre en resultados.
 * - Paginación de 10 registros.
 *
 * @param \Illuminate\Http\Request $request Parámetros de filtro.
 *
 * @return \Illuminate\Http\JsonResponse Resultado paginado.
 */

    // permiso = ver dependencias
    public function filtrarDependencias(Request $request){
        //$this->authorize('view', Dependencia::class);
        $query = Dependencia::with(['dependenciaPadre','direccion']);

        $roles = Auth::user()->getRoleNames();

        $rol = $roles[0] ;
        $id_dependencia =  Auth::user()->dependencia->id;

        if ($rol === 'Administrador de Dependencia') {
            $query->obtenerDependenciasInternas($id_dependencia);
        }

        elseif ($rol === 'Jefe de Area') {
            $query->where('id', $id_dependencia);
        }


        /* ----------------------
         FILTRO POR NOMBRE DE LA DEPENDENCIA
        ---------------------- */
        //filled se fija que exista y no este vacio
        if(!empty($request->filled('nombre')) && $request->input('nombre') != ''){
            $nombre = $request->input('nombre');
            $query->where('nombre', 'LIKE', "%{$nombre}%");
        }

        /* ----------------------
         FILTRO POR DEPENDENCIA PADRE
        ---------------------- */
        /**Obtiene la dependencia por la que se busca (el orWhere) y los sectores donde esta es una jerarquia superior
         * No incluye las áreas donde esta dependencia actúa como jerarquia indirecta (es decir, niveles inferiores más profundos)
*/
        if (!empty($request->filled('dependencia_padre')) && $request->input('dependencia_padre') != 'default') {
            $dependencia_padre = $request->input('dependencia_padre');
            $query->where(function ($q) use ($dependencia_padre) {
                $q->where('id_dependencia_padre', $dependencia_padre)
                ->orWhere('id', $dependencia_padre);
            });
        }

        /* ----------------------
         FILTRO POR SI ESTA ACTIVA
        ---------------------- */

        if (!empty($request->filled('activa')) && $request->input('activa') != 'default') {
            $activa = $request->input('activa');
            $query->where('activa', $activa);
        }

        /* ----------------------
         CALLE
        ---------------------- */

        if (!empty($request->filled('calle')) && $request->input('calle') != 'default') {
            $calle = $request->input('calle');
            $query->whereHas('direccion', function ($q) use ($calle) {
                $q->where('calle', 'LIKE', "%{$calle}%");
            });
        }

        /* ----------------------
         CIUDAD
        ---------------------- */

        if (!empty($request->filled('ciudad')) && $request->input('ciudad') != 'default') {
            $ciudad = $request->input('ciudad');
            $query->whereHas('direccion', function ($q) use ($ciudad) {
                $q->where('ciudad', $ciudad);
            });
        }

        /* ----------------------
         ORDENAMIENTO SEGURO
         //Campo por el que se ordena
        ---------------------- */
        $sortField = $request->input('sort_field', 'nombre');

        //Como se ordena
        $sortOrder = $request->input('sort_order', 'asc');

        $allowedSorts = ['nombre', 'activa'];
        $allowedOrders = ['asc', 'des'];

        if (!in_array($sortField, $allowedSorts)) {
            $sortField = 'nombre';
        }

        if (!in_array($sortOrder, $allowedOrders)) {
            $sortOrder = 'asc';
        }

/* ----------------------
         Fuerza que la dependencia padre por la cual se filtra aparezca en primer lugar del resultado.
         El resto de los registros se ordenan por nombre.
         Si no se filtra por dependencia_padre, TODOS los registros se ordenan por nombre.
*/

        if(!empty($request->filled('dependencia_padre'))){

            $prioridadId = $request->input('dependencia_padre');

            $query->orderByRaw(
                "CASE WHEN id = ? THEN 0 ELSE 1 END",
                [$prioridadId]
            )->orderBy('nombre');
        }
        else{
            $query->orderBy("nombre");
        }

        /* ----------------------
         PAGINACIÓN
        ---------------------- */
       $dependencias = $query->paginate(10);

        return response()->json($dependencias);

    }
}




