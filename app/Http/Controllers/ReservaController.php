<?php

namespace App\Http\Controllers;

use App\Http\Requests\FiltroReservasRequest;
use App\Models\Reserva;
use App\Services\ReservaService;
use App\Policies\ReservaPolicy;
use Illuminate\Support\Facades\Auth;

class ReservaController extends Controller{
    protected ReservaService $service;

    public function __construct(ReservaService $service)
    {
        $this->service = $service;
    }

public function index()
{
    $user = auth()->user();

    $query = Reserva::with(['vehiculo', 'dependencia_duena', 'estado_reserva']);

    if ($user->hasRole('Administrador General')) {
        // ve todo
    }
    elseif ($user->hasRole('Administrador de Dependencia')) {
        $query->where(function ($q) use ($user) {
            $q->where('id_dependencia_duena', $user->id_dependencia)
              ->orWhere('id_dependencia_solicitante', $user->id_dependencia);
        });
    }
    else {
        // usuario común
        $query->where('id_usuario', $user->id);
    }

    $reservas = $query->latest()->paginate(10);

    return view('dependencias.reservas.reservas', compact('reservas'));
}




    // permiso = ver dependencias
    public function verReservas(ReservaPolicy $ReservaP){

     if($this->authorize('view', $ReservaP)){

         $data = array_merge(
            ['reservas' => $this->service->verReservas()],
            $this->service->datosFiltros()
        );
        return view('dependencias.reservas.reservas', $data);
     }
    }


    // permiso = ver dependencias
    public function verReserva($id , ReservaPolicy $ReservaP){
         if($this->authorize('view', $ReservaP)){
        $reserva = $this->service->verReserva($id);
        return view('dependencias.reservas.reserva', $reserva);
         }
    }


    // permiso = eliminar dependencias
    public function cancelarReserva($id, ReservaPolicy $ReservaP){

       if($this->authorize('finalizar', $ReservaP)){
            $this->service->cancelarReserva($id);
            return redirect()->route('dependencias.reservas.reservas')->with('success', 'La dependencia fue eliminada correctamente.');
         }
            }



    // // permiso = crear dependencias
    // // datosRelacionDependencia = Recupera la información de las tablas relacionadas a la entidad Dependencia
    // public function datosParaCrearDependencia(){
    //     return view('dependencias.formulario-crear-editar.formCrear',$this->service->datosRelacionesDependencia());
    // }

    // // permiso = crear dependencias
    // public function crearDependencia(CrearDependenciaRequest $request){
    //     $this->service->crearDependencia($request->validated());
    //     return redirect()->route('dependencias.index')->with('success', 'La dependencia fue creada correctamente.');

    // }

    // // permiso = editar dependencias
    // public function datosParaEditarDependencia($id){
    //     return view('dependencias.formulario-crear-editar.formEditar',$this->service->datosRelacionesDependencia($id));
    // }

    // // permiso = editar dependencias
    // public function editarDependencia(EditarDependenciaRequest $request, $id){
    //     $this->service->editarDependencia($request->validated(), $id);
    //     return redirect()->route('dependencias.index')->with('success', 'La dependencia fue actualizada correctamente.');
    // }



    public function filtrarReservas(FiltroReservasRequest $request){

        $query = Reserva::with(['vehiculo','dependencia_duena', 'estado_reserva']);

       $user = Auth::user();

        // Solo puede ver las  reservas que involucran a la dependencia
       if ($user->hasRole('Administrador de Dependencia')) {
            $id = $request->input('id');
           $query->where(function ($q) use ($id) {
                $q->where('id', $id)
                ->orWhere('id_dependencia_duena', $id)
                ->orWhere('id_dependencia_solicitante', $id);
            });
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

public function reservas(){
    return View ('ui.reservas');
}
public function prestamos(){
    return View ('ui.prestamos');
}
}
