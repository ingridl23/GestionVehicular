<?php
namespace App\Http\Controllers;
use App\Models\Vehiculo;
use App\Models\Dependencia;
use App\Models\Direcciones;
use App\Models\EstadosNafta;
use App\Models\EstadosVehiculo;
use Illuminate\Http\Request;
use App\Services\VehiculoService;
use Illuminate\Http\JsonResponse;
use Exception;
use App\Http\Controllers\UserController;
use App\Policies\VehiculoPolicy;

class VehiculoController extends Controller
{

public function sectionVehiculo(){
    $dependencias =Dependencia::all();
//  ESTA LÍNEA para obtener los estados
    $estados = EstadosVehiculo::all();
   $vehiculos = Vehiculo::with([
    'estadoVehiculo',
    'estadoNafta',
    'dependenciaDuena',
    'direccionActual'
])->get();


    return View('components.vehiculos.vehiculos', compact('dependencias','vehiculos','estados') );
}
    // CU 2 – Listado
// En VehiculoController.php
public function index(Request $request, VehiculoService $service)
{
    return response()->json(
        $service->listar($request)
    );
}

    // CU 2 – Detalle
   public function show(Vehiculo $vehiculo): JsonResponse
{
$this->authorize('view', $vehiculo);
    $vehiculo->load([
        'estadoVehiculo',
        'estadoNafta',
        'dependenciaDuena',
        'direccionActual'
    ]);

    return response()->json([
        'id' => $vehiculo->id,
        'dominio' => $vehiculo->dominio,
        'marca' => $vehiculo->marca,
        'modelo' => $vehiculo->modelo,
        'anio' => $vehiculo->anio,
        'kilometros' => $vehiculo->kilometros,
        'control_satelital' => $vehiculo->control_satelital,
        'habilitado_prestamo' => $vehiculo->habilitado_prestamo,
        'condiciones_prestamo' => $vehiculo->condiciones_prestamo,
        'VTV' => $vehiculo->VTV,

        // 🔥 relaciones listas para JS
        'estado_vehiculo' => [
            'id' => $vehiculo->estadoVehiculo->id ?? null,
            'estado' => $vehiculo->estadoVehiculo->estado ?? null,
        ],
        'estado_nafta' => [
            'id' => $vehiculo->estadoNafta->id ?? null,
            'estado' => $vehiculo->estadoNafta->estado ?? null,
        ],
        'dependencia_duena' => [
            'id' => $vehiculo->dependenciaDuena->id ?? null,
            'nombre' => $vehiculo->dependenciaDuena->nombre ?? null,
        ],
        'direccion_actual' => [
            'id' => $vehiculo->direccionActual->id ?? null,
            'nombre' => $vehiculo->direccionActual->nombre ?? null,
        ],
    ]);
}

public function detalle(Vehiculo $vehiculo)
{
     $this->authorize('view', $vehiculo);

    return view('components.vehiculos.vehiculo-detalle', [
        'vehiculo' => $vehiculo,
        'dependencias' => Dependencia::all(),
        'direcciones' => Direcciones::all(),
        'estadosVehiculo' => EstadosVehiculo::all(),
        'estadosNafta' => EstadosNafta::all(),
    ]);
}


    // CU 5 – Crear
    public function store(Request $request, VehiculoService $service): JsonResponse
    {
         $this->authorize('create', Vehiculo::class);
        $data = $request->validate([
            'dominio' => 'required|string|unique:vehiculo,dominio',
            'marca' => 'required|string',
            'modelo' => 'required|string',
            'anio' => 'required|integer',
            'id_dependencia_duena' => 'required|exists:dependencias,id',
            'id_direccion_actual' => 'required|exists:direcciones,id',
            'id_estado_nafta' => 'required|exists:estados_nafta,id',
            'id_estado_vehiculo' => 'required|exists:estados_vehiculo,id',
            'kilometros' => 'required|integer|min:0',
            'VTV' => 'required|date',
        ]);


        $vehiculo = $service->crear($data);

        return response()->json([
            'message' => 'Vehículo agregado correctamente',
            'vehiculo' => $vehiculo
        ], 201);
    }

    // CU 4 – Modificar
    public function update(Request $request, Vehiculo $vehiculo, VehiculoService $service): JsonResponse
    {
        $this->authorize('update', $vehiculo);
        $data = $request->validate([
            'marca' => 'sometimes|string',
            'modelo' => 'sometimes|string',
            'anio' => 'sometimes|integer',
            'id_estado_vehiculo' => 'sometimes|exists:estados_vehiculo,id',
            'id_direccion_actual' => 'sometimes|exists:direcciones,id',
            'id_estado_nafta' => 'sometimes|exists:estados_nafta,id',
            'kilometros' => 'sometimes|integer|min:0',
            'VTV' => 'sometimes|date',
            'habilitado_prestamo' => 'sometimes|boolean',
            'condiciones_prestamo' => 'nullable|string',
            'control_satelital' => 'sometimes|boolean',
        ]);


        $vehiculo = $service->actualizar($vehiculo, $data);

        return response()->json([
            'message' => 'Vehículo actualizado',
            'vehiculo' => $vehiculo
        ]);
    }

    // CU 17 – Reasignar
    public function updateAsignacion(Request $request, Vehiculo $vehiculo, VehiculoService $service): JsonResponse
    {
          $this->authorize('modificarAsignacion', $vehiculo);
        $request->validate([
            'id_dependencia_duena' => 'required|exists:dependencias,id'
        ]);

        $vehiculo = $service->cambiarAsignacion(
            $vehiculo,
            $request->id_dependencia_duena
        );

        return response()->json([
            'message' => 'Asignación actualizada',
            'vehiculo' => $vehiculo
        ]);
    }

    // CU 3 – Eliminar
   public function destroy(Vehiculo $vehiculo, VehiculoService $service): JsonResponse
{
    $this->authorize('delete', $vehiculo);

    try {
        $service->eliminar($vehiculo);

        return response()->json([
            'message' => 'Vehículo dado de baja correctamente'
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => $e->getMessage()
        ], 422);
    }
}

}
