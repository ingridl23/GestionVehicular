<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use Illuminate\Http\Request;
use App\Services\VehiculoService;
use Illuminate\Http\JsonResponse;
use Exception;

class VehiculoController extends Controller
{
    // CU 2 – Listado
    public function index(Request $request, VehiculoService $service): JsonResponse
    {
        return response()->json(
            $service->listar($request)
        );
    }

    // CU 2 – Detalle
    public function show(Vehiculo $vehiculo): JsonResponse
    {
        return response()->json($vehiculo->load([
            'dependencia',
            'estado_vehiculo',
            'nafta',
            'direccion'
        ]));
    }

    // CU 5 – Crear
    public function store(Request $request, VehiculoService $service): JsonResponse
    {
        $data = $request->validate([
            'dominio' => 'required|string|unique:vehiculos,dominio',
            'marca' => 'required|string',
            'modelo' => 'required|string',
            'anio' => 'required|integer',
            'id_dependencia_duena' => 'required|exists:dependencias,id',
            'VTV' => 'required|date'
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
        $data = $request->validate([
            'marca' => 'sometimes|string',
            'modelo' => 'sometimes|string',
            'anio' => 'sometimes|integer',
            'id_estado_vehiculo' => 'sometimes|exists:estados_vehiculo,id',
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
        $service->eliminar($vehiculo);

        return response()->json([
            'message' => 'Vehículo dado de baja correctamente'
        ]);
    }
}
