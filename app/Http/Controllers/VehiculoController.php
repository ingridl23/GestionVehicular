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
use Illuminate\Support\Facades\Log;
use App\Exports\VehiculosExport;
use Maatwebsite\Excel\Facades\Excel;

/**
 * @class VehiculoController
 * @brief Controlador encargado de la gestión de vehículos institucionales.
 *
 * Permite:
 * - Listado
 * - Creación
 * - Edición
 * - Eliminación (baja)
 * - Reasignación de dependencia
 * - Consulta detallada en formato JSON
 *
 * La lógica de negocio se delega a VehiculoService.
 *
 * @package App\Http\Controllers
 * @since 2026
 */





class VehiculoController extends Controller
{

/**
 * Renderiza la seccion de vehiculos dentro del sistema
 * Muestra vehiculos con informacion como dependencia, estado del vehiculo, estado del combustible, direccion actual.
 * Permite mostrar y categorizar de manera uniforme y dinamico los vehiculos registrados dentro del sistema.
 */


public function sectionVehiculo(){
    $dependencias =Dependencia::all();
    $direcciones = Direcciones::all();
//  ESTA LÍNEA para obtener los estados
    $estados = EstadosVehiculo::all();
    $estadosNafta= EstadosNafta::all();
   $vehiculos = Vehiculo::with([
    'estadoVehiculo',
    'estadoNafta',
    'dependenciaDuena',
    'direccionActual'
])->get();


    return View('components.vehiculos.vehiculos', compact('dependencias','vehiculos','estados','direcciones','estadosNafta') );
}


/**
 * Devuelve el detalle de un vehículo en formato JSON.
 *
 * Incluye relaciones:
 * - Estado del vehículo
 * - Estado de nafta
 * - Dependencia dueña
 * - Dirección actual
 *
 * @param \App\Models\Vehiculo $vehiculo
 * @return \Illuminate\Http\JsonResponse
 */
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
        'vtv' => $vehiculo->VTV,

        // relaciones listas para JS
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


/**
 * Permite renderizar el detalle de un vehiculo seleccionado.
 * Segun rol de usuario podra modificar la informaion y el estado del vehiculo.
 */

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


/**
 * Permite crear y persistir un vehiculo dentor del sistema
 *
 * Incluye relaciones:
 * - Estado del vehículo
 * - Estado de nafta
 * - Dependencia dueña
 * - Dirección actual
 *
 * @param \App\Models\Vehiculo $vehiculo
 * @return \Illuminate\Http\JsonResponse
 */

    // CU 5 – Crear
    public function store(Request $request, VehiculoService $service): JsonResponse
    {
        try {
            $this->authorize('create', Vehiculo::class);

            Log::info('Creando nuevo vehículo', [
                'datos_recibidos' => $request->all()
            ]);

            $data = $request->validate([
                'dominio' => 'required|string|unique:vehiculos,dominio',
                'marca' => 'required|string',
                'modelo' => 'required|string',
                'anio' => 'required|integer',
                'id_dependencia_duena' => 'required|exists:dependencias,id',
                'id_direccion_actual' => 'required|exists:direcciones,id',
                'id_estado_nafta' => 'required|exists:estados_naftas,id',
                'id_estado_vehiculo' => 'required|exists:estados_vehiculos,id',
                'kilometros' => 'required|integer|min:0',
                'vtv' => 'required|date',
               'habilitado_prestamo' => 'required|boolean',
               'control_satelital' => 'required|boolean',

                'condiciones_prestamo' => 'nullable|string',
            ]);

            $vehiculo = $service->crear($data);

            // Recargar relaciones
            $vehiculo->load([
                'estadoVehiculo',
                'estadoNafta',
                'dependenciaDuena',
                'direccionActual'
            ]);

            Log::info('Vehículo creado exitosamente', [
                'vehiculo_id' => $vehiculo->id
            ]);

            return response()->json([
                'message' => 'Vehículo agregado correctamente',
                'vehiculo' => $vehiculo
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación al crear vehículo', [
                'errors' => $e->errors()
            ]);

            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error al crear vehículo', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error al crear el vehículo: ' . $e->getMessage()
            ], 500);
        }
    }

/**
 * Actualiza un vehículo existente.
 *
 * Permite actualización parcial de campos.
 * Recarga relaciones luego de actualizar.
 *
 * @param \Illuminate\Http\Request $request
 * @param \App\Models\Vehiculo $vehiculo
 * @param \App\Services\VehiculoService $service
 * @return \Illuminate\Http\JsonResponse
 */
    // CU 4 – Modificar
       public function update(Request $request, Vehiculo $vehiculo, VehiculoService $service): JsonResponse
    {
        try {
            $this->authorize('update', $vehiculo);

            // Log para debugging
            Log::info('Actualizando vehículo', [
                'vehiculo_id' => $vehiculo->id,
                'datos_recibidos' => $request->all()
            ]);

            $data = $request->validate([
                'dominio' => 'sometimes|string',
                'marca' => 'sometimes|string',
                'modelo' => 'sometimes|string',
                'anio' => 'sometimes|integer',
                'id_estado_vehiculo' => 'sometimes|exists:estados_vehiculos,id',
                'id_direccion_actual' => 'sometimes|exists:direcciones,id',
                'id_dependencia_duena' => 'sometimes|exists:dependencias,id',
                'id_estado_nafta' => 'sometimes|exists:estados_naftas,id',
                'kilometros' => 'sometimes|integer|min:0',
                'VTV' => 'sometimes|date',
                'habilitado_prestamo' => 'sometimes|boolean',
                'condiciones_prestamo' => 'nullable|string',
                'control_satelital' => 'sometimes|boolean',
            ]);

            $vehiculoActualizado = $service->actualizar($vehiculo, $data);

            // Recargar relaciones
            $vehiculoActualizado->load([
                'estadoVehiculo',
                'estadoNafta',
                'dependenciaDuena',
                'direccionActual'
            ]);

            Log::info('Vehículo actualizado exitosamente', [
                'vehiculo_id' => $vehiculoActualizado->id
            ]);

            return response()->json([
                'message' => 'Vehículo actualizado correctamente',
                'vehiculo' => $vehiculoActualizado
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación', [
                'errors' => $e->errors()
            ]);

            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error al actualizar vehículo', [
                'vehiculo_id' => $vehiculo->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error al actualizar el vehículo: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Metodo inicial para la carga visual de vehiculos
     */
    public function index(Request $request, VehiculoService $service)
{
    $this->authorize('viewAny', Vehiculo::class);

    return response()->json(
        $service->listar($request)
    );
}

/**
 * Cambia la dependencia asignada a un vehículo.
 *
 * @param \Illuminate\Http\Request $request
 * @param \App\Models\Vehiculo $vehiculo
 * @param \App\Services\VehiculoService $service
 * @return \Illuminate\Http\JsonResponse
 */
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
/**
 * Dar de baja un vehículo.
 *
 * @param \App\Models\Vehiculo $vehiculo
 * @param \App\Services\VehiculoService $service
 * @return \Illuminate\Http\JsonResponse
 */
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

/**
 * Permite generar un documento excel con la totalidad de vehiculos y sus datos registrados en el sistema.
 * Posteriormente garantiza a la gestion administrativa de las dependencia un mejor control de segumienwto de uso y gasto
 * de recursos en reservas y posesion de vehiculos y utilizacion real de los empleados.
 */

public function export()
{
    return Excel::download(
        new VehiculosExport,
        'vehiculos_gestionVehicular.xlsx'
    );
}
}
