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

/**
 * @class GastoController
 * @brief Controlador encargado de la gestión de gastos asociados a viajes.
 *
 * Permite:
 * - Previsualizar el gasto estimado de un viaje
 * - Generar y persistir un gasto
 * - Consultar gasto individual por viaje
 * - Listar todos los gastos
 * - Obtener estadísticas generales
 *
 * La lógica de negocio se delega a GastoService.
 * La autorización se gestiona mediante Policies de Laravel.
 *
 * Fórmula de cálculo del gasto:
 * importe = litros_consumidos * valor_litro
 *
 * @package App\Http\Controllers
 * @author Ingrid Ledesma
 * @version 1.0
 * @since 2026
 */


class GastoController extends Controller{
/**
 * Previsualiza el monto estimado de un gasto para un viaje.
 *
 * No persiste datos en la base.
 * Utilizado para mostrar al usuario el cálculo antes de confirmar.
 *
 * @param int $viajeId Identificador del viaje.
 * @param GastoService $service Servicio de cálculo de gastos.
 * @return \Illuminate\Http\JsonResponse Devuelve el monto estimado.
 * @throws \Exception Si ocurre un error en el cálculo.
 */

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

    /**
 * Genera y registra el gasto correspondiente a un viaje.
 *
 * Requiere autorización mediante la policy 'create'.
 * Persiste el gasto en la base de datos.
 *
 * @param int $viajeId Identificador del viaje.
 * @param GastoService $service Servicio encargado de generar el gasto.
 * @return \Illuminate\Http\JsonResponse Devuelve el gasto creado.
 * @throws \Illuminate\Auth\Access\AuthorizationException
 * @throws \Exception Si falla la generación del gasto.
 */

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

/**
 * Obtiene el gasto asociado a un viaje específico.
 *
 * Busca el gasto por id_viaje.
 * Si no existe, devuelve error 404.
 *
 * @param int $viajeId Identificador del viaje.
 * @return \Illuminate\Http\JsonResponse Información del gasto.
 * @throws \Illuminate\Auth\Access\AuthorizationException
 */
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

/**
 * Devuelve el listado completo de gastos.
 *
 * Incluye la relación con el viaje asociado.
 * Requiere autorización 'viewAny'.
 *
 * @return \Illuminate\Http\JsonResponse Lista de gastos.
 * @throws \Illuminate\Auth\Access\AuthorizationException
 */

    public function index(): JsonResponse
    {
         $this->authorize('viewAny', Gasto::class);

        return response()->json(
            Gasto::with('viaje')->get()
        );
    }

/**
 * Devuelve estadísticas generales de los gastos registrados.
 *
 * Calcula:
 * - Cantidad total de gastos
 * - Suma total
 * - Promedio
 * - Gasto máximo
 * - Gasto mínimo
 *
 * Requiere autorización 'viewResumen'.
 *
 * @return \Illuminate\Http\JsonResponse Estadísticas agregadas.
 * @throws \Illuminate\Auth\Access\AuthorizationException
 */

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
