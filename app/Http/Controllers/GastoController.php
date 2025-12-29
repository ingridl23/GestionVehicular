<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use App\Models\Viaje;
use Illuminate\Http\Request;
use App\Services\GastoService;
use Illuminate\Http\JsonResponse;
use Exception;
class GastoController extends Controller{


    /**
     * GastoController
├── index()        // listado de gastos
├── calcular()     // cálculo por viaje
├── resumen()      // totales y estadísticas
├── show($viaje)   // gasto de un viaje puntual

     */


    /**importe =
    (litros_consumidos * valor_litro)

     */
    public function preview(int $viajeId, GastoService $service): JsonResponse
    {
        try {
            $monto = $service->calcularGastoPorViaje($viajeId);

            return response()->json([
                'viaje_id' => $viajeId,
                'monto_estimado' => $monto
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }


    public function calcular(int $viajeId, GastoService $service): JsonResponse
    {
        try {
            $gasto = $service->generarGastoPorViaje($viajeId);

            return response()->json([
                'message' => 'Gasto generado correctamente',
                'gasto'   => $gasto
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
