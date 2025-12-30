<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use App\Models\Viaje;
use Illuminate\Http\Request;
use App\Services\GastoService;
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

    public function show ($viajeId){
    try{

        if($viajeId > 0 && $viajeId != 0){
                $viaje = Gasto::where('id_viaje', $viajeId)->first();

                if(!$viaje){
                    return response()->json([
                        'error' => 'El viaje no tiene gasto asociado'
                    ], 404);
                 }
                return response()->json($viaje);
        }
    }catch (Exception $e){
            return response()->json([
                'error' => $e->getMessage()
            ], 404);
    }



    }

//index



    public function index(): JsonResponse
    {
        return response()->json(
            Gasto::with('viaje')->get()
        );
    }



    //resumen

    public function resumen(): JsonResponse
    {
        return response()->json([
            'cantidad_gastos' => Gasto::count(),
            'gasto_total' => Gasto::sum('monto'),
            'gasto_promedio' => Gasto::avg('monto'),
            'max_gasto' => Gasto::max('monto'),
            'min_gasto' => Gasto::min('monto'),
        ]);
    }
}
