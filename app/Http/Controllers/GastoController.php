<?php

namespace App\Http\Controllers;
use App\Models\Gasto;
use App\Models\Viaje;
use Illuminate\Http\Request;
use App\Services\GastoService;
use App\Policies\GastoPolicy;
use Illuminate\Http\JsonResponse;
use Exception;

use function PHPUnit\Framework\isEmpty;

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
            $monto = $service->generarGastoPorViaje($viajeId);

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
        $this->authorize('create', Gasto::class);
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


    //show

    public function show ($viajeId, GastoPolicy $GastoP){


         $gasto = Gasto::where('id_viaje', $viajeId)->first();

        if (! $gasto) {
        return response()->json([
            'error' => 'El viaje no tiene gasto asociado'
        ], 404);
    }

    $this->authorize('view', $gasto);

    return response()->json($gasto);


    }

//index



    public function index(): JsonResponse
    {
         $this->authorize('viewAny', Gasto::class);

        return response()->json(
            Gasto::with('viaje')->get()
        );
    }



    //resumen

    public function resumen( GastoPolicy $GastoP): JsonResponse
    {
        $this->authorize('viewResumen', Gasto::class);
        return response()->json([
            'cantidad_gastos' => Gasto::count(),
            'gasto_total' => Gasto::sum('monto'),
            'gasto_promedio' => Gasto::avg('monto'),
            'max_gasto' => Gasto::max('monto'),
            'min_gasto' => Gasto::min('monto'),
        ]);
    }
}
