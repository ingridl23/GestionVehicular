<?php

namespace App\Http\Controllers;
use App\Models\Alerta;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Enums\TipoAlerta;
/**
 * @class AlertaController
 * @brief Controlador encargado de la gestión de alertas del sistema.
 *
 * Este controlador administra la visualización, consulta y resolución
 * de alertas generadas por diferentes eventos del sistema de gestión vehicular.
 *
 * Las alertas pueden estar asociadas a:
 * - Vencimiento de licencias
 * - Mantenimiento pendiente de vehículos
 * - Vehículos fuera de servicio
 * - Reservas rechazadas
 * - Combustible bajo
 *
 * Funcionalidades principales:
 * - Mostrar listado paginado de alertas activas
 * - Proveer estadísticas para dashboard
 * - Exponer API para alertas recientes (navbar)
 * - Filtrar alertas por entidad relacionada
 * - Resolver (desactivar) alertas
 *
 * @package App\Http\Controllers
 */
class AlertaController extends Controller
{
    /**
     * Mostrar vista de alertas
     */

    /**
 * Mostrar vista principal de alertas activas.
 *
 * Obtiene todas las alertas activas y genera estadísticas
 * agrupadas por tipo para ser utilizadas en el panel de visualización.
 *
 * Reglas de negocio:
 * - Solo se muestran alertas activas.
 * - Se ordenan por fecha de generación descendente.
 * - Se paginan en bloques de 20 registros.
 *
 * Estadísticas generadas:
 * - Total de alertas activas
 * - Cantidad de alertas por tipo (licencias, mantenimiento, vehículos)
 *
 * @return View Vista con listado paginado y estadísticas de alertas.
 */
  public function index(): View
{
    $alertasQuery = Alerta::where('activa', true);

    $stats = [
        'total' => (clone $alertasQuery)->count(),
        'licencias' => (clone $alertasQuery)->where('tipo', 'licencia_vencimiento')->count(),
        'mantenimiento' => (clone $alertasQuery)->where('tipo', 'mantenimiento_pendiente')->count(),
        'vehiculos' => (clone $alertasQuery)->where('tipo', 'vehiculo_fuera_servicio')->count(),
    ];

    $alertas = $alertasQuery
        ->orderBy('fecha_generada', 'desc')
        ->paginate(20);

    return view('components.alertas', compact('alertas', 'stats'));
}


    /**
     * API: Obtener alertas recientes para el navbar
     */

    /**
 * Obtener alertas recientes para el navbar (API).
 *
 * Devuelve las últimas 5 alertas activas ordenadas por fecha de generación.
 * Se transforma la colección para enviar únicamente los campos necesarios
 * al frontend.
 *
 * Reglas de negocio:
 * - Solo alertas activas.
 * - Máximo 5 resultados.
 * - Formateo de fecha en formato relativo (diffForHumans).
 *
 * @return JsonResponse Lista de alertas recientes en formato JSON.
 */
    public function recientes(): JsonResponse
    {


        $alertas = Alerta::where('activa', true)
            ->latest('fecha_generada')
            ->limit(5)
            ->get()
            ->map(function($alerta) {
                return [
                    'id' => $alerta->id,
                    'titulo' => $alerta->tipo ?? 'Alerta',
                    'mensaje' => $alerta->mensaje,
                    'icono' => $alerta->icono,
                    'color' => $alerta->color,
                    'fecha' => $alerta->fecha_generada->diffForHumans(),
                ];
            });

        return response()->json($alertas);
    }

    /**
     * API: Alertas por entidad
     */
    public function porEntidad(string $tipo, int $id): JsonResponse
    {
        $alertas = Alerta::where('entidad_tipo', $tipo)
            ->where('entidad_id', $id)
            ->where('activa', true)
            ->get();

        return response()->json($alertas);
    }

    /**
     * Resolver/marcar alerta como leída
     */
    public function resolver(int $id): JsonResponse
    {
        $alerta = Alerta::findOrFail($id);

        $alerta->update([
            'activa' => false,
            'fecha_resuelta' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alerta resuelta correctamente'
        ]);
    }
public function resolverMultiples(Request $request): JsonResponse
{
    $ids = $request->input('ids', []);

    if (empty($ids)) {
        return response()->json([
            'success' => false,
            'message' => 'No se enviaron IDs'
        ], 400);
    }

    Alerta::whereIn('id', $ids)->update([
        'activa' => false,
        'fecha_resuelta' => now()
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Alertas resueltas correctamente'
    ]);
}

public function resolverTodas(): JsonResponse
{
    Alerta::where('activa', true)->update([
        'activa' => false,
        'fecha_resuelta' => now()
    ]);

    return response()->json([
        'success' => true
    ]);
}

public function show(int $id): JsonResponse
{
    $alerta = Alerta::findOrFail($id);

    $dependencia = 'Sin dependencia';

    if ($alerta->entidad_tipo === 'usuario') {
        $usuario = $alerta->usuario()->with('dependencia')->first();

        if ($usuario && $usuario->dependencia) {
            $dependencia = $usuario->dependencia->nombre;
        }
    }

    return response()->json([
        'id' => $alerta->id,
        'tipo' => $alerta->tipo,
        'mensaje' => $alerta->mensaje,
        'fecha' => $alerta->fecha_generada->format('d/m/Y H:i'),
        'dependencia' => $dependencia,
    ]);
}
    /**
     * Obtener ícono según tipo de alerta
     */
    /*
    private function getIcono(string $tipo): string
    {
        return match($tipo) {
            'licencia_vencimiento' => 'fa-id-card',
            'vehiculo_fuera_servicio' => 'fa-car-crash',
            'reserva_rechazada' => 'fa-calendar-times',
            'mantenimiento_pendiente' => 'fa-wrench',
            'combustible_bajo' => 'fa-gas-pump',
            default => 'fa-bell'
        };
    }

    /**
     * Obtener color según tipo de alerta
     *//*
    private function getColor(string $tipo): string
    {
        return match($tipo) {
            'licencia_vencimiento' => 'yellow',
            'vehiculo_fuera_servicio' => 'red',
            'reserva_rechazada' => 'orange',
            'mantenimiento_pendiente' => 'blue',
            'combustible_bajo' => 'yellow',
            default => 'blue'
        };
    }*/
}
