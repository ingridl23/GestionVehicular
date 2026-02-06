<?php

namespace App\Http\Controllers;
use App\Models\Dependencia;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;
use function PHPUnit\Framework\isEmpty;

use App\Http\Requests\FiltroDependenciasRequest;
use App\Http\Requests\EditarDependenciaRequest;
use App\Http\Requests\CrearDependenciaRequest;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use App\Services\DependenciaService;
use App\Policies\DependenciaPolicy;
use Illuminate\Support\Facades\Auth;

class DependenciaController extends Controller{

    protected DependenciaService $service;

    public function __construct(DependenciaService $service)
    {
        $this->service = $service;
    }

    // permiso = ver dependencias
    public function verDependencias(){
        $this->authorize('vistaGeneral', Dependencia::class);
         $data = array_merge(
            ['dependencias' => $this->service->verDependencias()],
            $this->service->datosFiltros()
        );
        return view('ui.dependencias.dependencias', $data);
    }


    // permiso = ver dependencias
    public function verDependencia($id){
        $dependencia = $this->service->verDependencia($id);
        $dependencia_autorizar = Dependencia::findOrFail($id);

        $this->authorize('view', $dependencia_autorizar);

        return view('ui.dependencias.dependencia', $dependencia);
    }


    // permiso = eliminar dependencias
    public function eliminarDependencia($id){
        try {

            $dependencia = $this->service->verDependencia($id);

            $this->authorize('delete', $dependencia);
            $this->service->eliminarDependencia($id);
            return redirect()->route('dependencias.index')->with('success', 'La dependencia fue eliminada correctamente.');
        } catch (ValidationException $e) {
             return redirect()->route('dependencias.index')
                ->withErrors($e->errors());
        }
    }

    // permiso = ver dependencias
    // Actualiza el estado de la dependencia, alternando entre activa (1) e inactiva (0) según su estado actual.
    public function cambiarActivaDependencia($id){
     $dependencia = $this->service->verDependencia($id);

    $this->authorize('toggle', $dependencia);

    $this->service->cambiarActivaDependencia($id);
    }


    // permiso = crear dependencias
    // datosRelacionDependencia = Recupera la información de las tablas relacionadas a la entidad Dependencia
  public function datosParaCrearDependencia(Request $request)
{
    $dependenciaPadre = null;

    if ($request->filled('id_dependencia_padre')) {
        $dependenciaPadre = Dependencia::findOrFail(
            $request->id_dependencia_padre
        );
    }

    $this->authorize('create', [Dependencia::class, $dependenciaPadre]);

    return view(
        'dependencias.formulario-crear-editar.formCrear',
        $this->service->datosRelacionesDependencia()
    );
}


    // permiso = crear dependencias
  public function crearDependencia(CrearDependenciaRequest $request)
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


    // permiso = editar dependencias
   public function datosParaEditarDependencia($id)
{
    $dependencia = $this->service->verDependencia($id);

    $this->authorize('update', $dependencia);

    return view(
        'dependencias.formulario-crear-editar.formEditar',
        $this->service->datosRelacionesDependencia($id)
    );
}


    // permiso = editar dependencias
   public function editarDependencia(EditarDependenciaRequest $request,$id) {
    $dependencia = $this->service->verDependencia($id);

    $this->authorize('update', $dependencia);

    $this->service->editarDependencia(
        $request->validated(),
        $id
    );

    return redirect()
        ->route('dependencias.index')
        ->with('success', 'La dependencia fue actualizada correctamente.');
}



    // permiso = ver dependencias
    public function filtrarDependencias(FiltroDependenciasRequest $request, DependenciaPolicy $DependenciaP){
$this->authorize('view', Dependencia::class);
        $query = Dependencia::with(['dependenciaPadre','direccion']);

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
        //Obtiene la dependencia por la que se busca (el orWhere) y los sectores donde esta es una jerarquia superior
        // No incluye las áreas donde esta dependencia actúa como jerarquia indirecta (es decir, niveles inferiores más profundos)

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
         LOCALIDAD
        ---------------------- */

        if (!empty($request->filled('localidad')) && $request->input('localidad') != 'default') {
            $localidad = $request->input('localidad');
            $query->whereHas('direccion', function ($q) use ($localidad) {
                $q->where('ciudad', $localidad);
            });
        }

        /* ----------------------
         ORDENAMIENTO SEGURO
        ---------------------- */

        //Campo por el que se ordena
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


        // Fuerza que la dependencia padre por la cual se filtra aparezca en primer lugar del resultado.
        // El resto de los registros se ordenan por nombre.

        // Si no se filtra por dependencia_padre, TODOS los registros se ordenan por nombre.
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




