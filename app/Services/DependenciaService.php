<?php

namespace App\Services;
use App\Models\Dependencia;
use App\Models\Direcciones;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DependenciaService{

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

    public function verDependencias(){
        $rol = $this->rol();
        $id_dependencia = $this->user()->dependencia->id;

        $query = Dependencia::with('direccion');

        if ($rol === 'Administrador de Dependencia') {
            $query->obtenerDependenciasInternas($id_dependencia);
        }

        elseif ($rol === 'Jefe de Area') {
            $query->where('id', $id_dependencia);
        }

        $dependencias = $query->orderBy('nombre')->paginate(10);

        return $dependencias;
    }

    public function datosFiltros(){
        return [
            'dependencias_filtros' => Dependencia::obtenerTodosLosPadres(),
            'localidades'  => Direcciones::obtenerLocalidades(),
        ];
    }

    //verDependencia Muestra los datos de la dependencia seleccionada, a que dependencia padre pertenece y si tiene dependencias hijas
    public function verDependencia($id){
        $datos_dependencia = Dependencia::with(['dependenciaPadre', 'direccion'])->findOrFail($id);
        
        $dependencia = Dependencia::with('dependenciasHijas')->find($id);

        $idsPermitidos = array_merge(
            [],
            $dependencia->obtenerIdsHijas()
        );

        $hijas = Dependencia::whereIn('id', $idsPermitidos)->get();

        return [
            'dependencias_hijas' => $hijas,
            'datos_dependencia' => $datos_dependencia,
        ];
    }


    public function eliminarDependencia($id){
       $dependencia = Dependencia::findOrFail($id);

       //Se comprueba que no tenga dependencias hijas
        if ($dependencia->dependenciasHijas()->exists()) {
            throw ValidationException::withMessages([
                'dependencia' => 'No se puede eliminar esta dependencia porque tiene dependencias hijas.',
            ]);
        }

        return $dependencia->delete();
    }
    

    
    public function cambiarActivaDependencia($id){
        $dependencia = Dependencia::findOrFail($id);

       //Se comprueba que no tenga dependencias hijas
        if ($dependencia->dependenciasHijas->isNotEmpty()) {
            throw ValidationException::withMessages([
                'dependencia' => 'No se puede desactivar esta dependencia porque existen otros registros que dependen de ella.',
            ]);
        }

        $valorActiva = ($dependencia->activa == true) ? false : true;
        $dependencia->update(['activa' => $valorActiva]);
    }



    public function editarDependencia(array $data, $id){
        $dependencia = Dependencia::findOrFail($id);

         if ($data['id_direccion'] === 'nueva') {
            $calle = $data['calle'];
            $altura = $data['altura'];
            $ciudad = $data['ciudad'];
            $id_direccion = $this->direccionService->crearDireccion($calle, $altura, $ciudad);
        } else {
            $id_direccion = $data['id_direccion'];
        }

        $nombre = $data['nombre'];
        $activa = filter_var($data['activa'], FILTER_VALIDATE_BOOLEAN);
        $id_dependencia_padre = $data['id_dependencia_padre'];
        $dependencia->update([
            'nombre' => $nombre,
            'id_dependencia_padre' => $id_dependencia_padre,
            'id_direccion' => $id_direccion,
            'activa' => $activa,
        ]);
    }

    public function datosRelacionesDependencia($id = null){
        return [
            'dependencias_arbol' => Dependencia::orderBy('nombre')->get(),
            'direcciones'  => Direcciones::orderBy('calle')->get(),
            'dependencia'  => $id ? Dependencia::with('direccion')->findOrFail($id) : null,
        ];
    }


    public function crearDependencia(array $data){
        if ($data['id_direccion'] === 'nueva') {
            $calle = $data['calle'];
            $altura = $data['altura'];
            $ciudad = $data['ciudad'];
            $id_direccion = $this->direccionService->crearDireccion($calle, $altura, $ciudad);
        } else {
            $id_direccion = $data['id_direccion'];
        }

        $nombre = $data['nombre'];
        $activa = filter_var($data['activa'], FILTER_VALIDATE_BOOLEAN);
        if($data['id_dependencia_padre'] != ""){
            $id_dependencia_padre = intval($data['id_dependencia_padre']);
        }
        else{
            $id_dependencia_padre = null;
        }
        
        $dependencia = Dependencia::create([
            'id_dependencia_padre' => $id_dependencia_padre,
            'nombre' => $nombre,
            'id_direccion' => $id_direccion,
            'activa' => $activa,
        ]);
    }

}