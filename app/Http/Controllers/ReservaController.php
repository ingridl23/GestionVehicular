<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrearReservaRequest;
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




    // permiso = ver_reservas_internas
    public function verReservasInternas(){
        $this->authorize('viewAny', Reserva::class);
        $data = array_merge(
            ['reservas' => $this->service->verReservasInternas()],
            $this->service->datosFiltrosInternas(),
            ['ubicacion' => 'interna'],
        );
        return view('reservas.reservas', $data);
     }



    // permiso = ver dependencias
    public function verReserva($id , ReservaPolicy $ReservaP){
         if($this->authorize('view', $ReservaP)){
        $reserva = $this->service->verReserva($id);
        return view('reservas.reserva', $reserva);
         }
    }


    // permiso = eliminar dependencias
    public function cancelarReserva($id, ReservaPolicy $ReservaP){

       if($this->authorize('finalizar', $ReservaP)){
            $this->service->cancelarReserva($id);
            return redirect()->route('reservas.reservas')->with('success', 'La dependencia fue eliminada correctamente.');
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



    public function filtrarReservasExternas(FiltroReservasRequest $request){
        $rol = $this->service->rol();
        $id_dependencia = $this->service->user()->dependencia->id;
        $query = Reserva::with('estado_reserva', 'vehiculo', 'usuario', 'dependencia_solicitante')->orderBy('fecha_inicio_reserva');

        $rol = mb_strtolower(Auth::user()->rol , 'UTF-8');

        // Solo puede ver las  reservas que involucran a la dependencia
        if($rol == 'administrador de dependencia'){
            $id = $request->input('id');
           $query->where(function ($q) use ($id) {
                $q->where('id', $id)
                ->orWhere('id_dependencia_duena', $id)
                ->orWhere('id_dependencia_solicitante', $id);
            });
        }

        /* ----------------------
         FILTRO POR NOMBRE DE LA DEPENDENCIA SOLICITANTE O POR NOMBRE O APELLIDO DEL CONDUCTOR
        ---------------------- */

        //filled se fija que exista y no este vacio
        if(!empty($request->filled('nombre')) && $request->input('nombre') != ''){
            $nombre = $request->input('nombre');
            $query->where(function ($q) use ($nombre) {

            // Dependencia solicitante
            $q->whereHas('dependencia_solicitante', function ($q2) use ($nombre) {
                $q2->where('nombre', 'LIKE', "%{$nombre}%");
            })

            // OR Usuario
            ->orWhereHas('usuario', function ($q3) use ($nombre) {
                $q3->where('name', 'LIKE', "%{$nombre}%")
                ->orWhere('lastname', 'LIKE', "%{$nombre}%");
            });

        });
        }

        /* ----------------------
         FILTRO POR SI EL ESTADO DE LA RESERVA
        ---------------------- */

        if (!empty($request->filled('estado')) && $request->input('estado') != 'default') {
            $activa = $request->input('estado');
            $query->where('id_estado_reserva', $activa);
        }

        /* ----------------------
         VEHICULO
        ---------------------- */

        if (!empty($request->filled('vehiculo')) && $request->input('vehiculo') != 'default') {
            $vehiculo = $request->input('vehiculo');
            $query->where('id_vehiculo', $vehiculo);
            // $query->whereHas('direccion', function ($q) use ($localidad) {
            //     $q->where('ciudad', $localidad);
            // });
        }

        /* ----------------------
         FECHA DE INICIO
        ---------------------- */

        if (!empty($request->filled('fecha_inicio')) && $request->input('fecha_inicio') != '') {
            $fecha_inicio = $request->input('fecha_inicio');
            $query->whereDate('fecha_inicio_reserva', $fecha_inicio);
        }

        /* ----------------------
         FECHA DE FIN
        ---------------------- */

        if (!empty($request->filled('fecha_fin')) && $request->input('fecha_fin') != '') {
            $fecha_fin = $request->input('fecha_fin');
            $query->whereDate('fecha_fin_reserva', $fecha_fin);
        }

        /* ----------------------
         ORDENAMIENTO SEGURO
        ---------------------- */

        //Campo por el que se ordena
        $sortField = $request->input('sort_field', 'fecha_inicio_reserva');

        //Como se ordena
        $sortOrder = $request->input('sort_order', 'asc');

        $allowedSorts = ['fecha_inicio_reserva', 'fecha_reserva'];
        $allowedOrders = ['asc', 'des'];

        if (!in_array($sortField, $allowedSorts)) {
            $sortField = 'fecha_inicio';
        }

        if (!in_array($sortOrder, $allowedOrders)) {
            $sortOrder = 'asc';
        }


        $query->orderBy($sortField, $sortOrder);

        /* ----------------------
         PAGINACIÓN
        ---------------------- */
       $dependencias = $query->paginate(10);

        return response()->json($dependencias);
    }


}

