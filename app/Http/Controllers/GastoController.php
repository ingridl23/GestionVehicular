<?php

namespace App\Http\Controllers;
use App\Models\Gasto;
use App\Models\Viaje;
use Illuminate\Http\Request;
use App\Services\GastoService;
use App\Policies\GastoPolicy;
use Illuminate\Http\JsonResponse;
use Exception;
use App\Exports\GastosExport;
use Maatwebsite\Excel\Facades\Excel;
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
 * Columna 1 — Formulario manual: litros, precio/litro y km opcionales.
 *  La lógica replica exactamente CalculoGastoService::calcularMonto() (litros × precio).
 *  Si $precioLitroActual viene del controller, se precarga automáticamente con un botón "Usar precio actual".
   Columna 2 — Resultado: muestra el monto estimado con detalle. Si se cargan km, calcula además costo por km y consumo en L/100km.
   Columna 3 — Estadísticas: consume $resumenGastos que ya devuelve tu GastoController::resumen().
    Mientras no lo pases, muestra placeholders con el nombre de la variable para que sepas qué agregar.
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


    /**
     * Devuelve estadísticas generales de los gastos registrados.
     *
     * Calcula cantidad, suma total, promedio, máximo y mínimo.
     * Requiere autorización 'viewResumen'.
     *
     * @return JsonResponse Estadísticas agregadas.
     */
    public function resumen(): JsonResponse
    {
        $this->authorize('viewResumen', Gasto::class);

        return response()->json([
            'cantidad_gastos' => Gasto::count(),
            'gasto_total'     => Gasto::sum('monto'),
            'gasto_promedio'  => Gasto::avg('monto'),
            'max_gasto'       => Gasto::max('monto'),
            'min_gasto'       => Gasto::min('monto'),
        ]);
    }

    /**
     * Vista de la calculadora de gastos para el dashboard de Auditoría.
     *
     * Provee al blade:
     * - $resumenGastos: estadísticas generales (columna 3 de la calculadora)
     * - $precioLitroActual: precio de hoy si existe en la tabla precio_combustible
     *
     * Uso desde AuditoriaController o directamente si tiene ruta propia:
     *   Route::get('/gastos/calculadora', [GastoController::class, 'calculadora'])->name('gastos.calculadora');
     *
     * O bien, llamar a getDatosCalculadora() desde el AuditoriaController
     * para incluir estos datos en la vista del dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function calculadora()
    {
        $this->authorize('viewResumen', Gasto::class);

        return view('admin.auditoria.index', $this->getDatosCalculadora());
    }

    /**
     * Prepara los datos necesarios para la sección de calculadora en el dashboard.
     *
     * Se puede llamar desde AuditoriaController así:
     *
     *   use App\Http\Controllers\GastoController;
     *   ...
     *   $datosCalculadora = (new GastoController)->getDatosCalculadora();
     *   return view('auditoria.index', array_merge($datos, $datosCalculadora));
     *
     * @return array
     */
    public function getDatosCalculadora(): array
    {
        return [
            'resumenGastos' => [
                'cantidad_gastos' => Gasto::count(),
                'gasto_total'     => Gasto::sum('monto'),
                'gasto_promedio'  => Gasto::avg('monto'),
                'max_gasto'       => Gasto::max('monto'),
                'min_gasto'       => Gasto::min('monto'),
            ],
            'precioLitroActual' => PrecioCombustible::where('fecha', Carbon::today())
                ->value('precio_litro'),
        ];
    }

    /**
     * Exporta los gastos de los últimos 6 meses en formato Excel.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */

public function export()
{
    return Excel::download(new GastosExport, 'gastos_ultimos_6_meses.xlsx');
}
}
