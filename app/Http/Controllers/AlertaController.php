<?php

namespace App\Http\Controllers;
use App\Models\Alerta;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Enums\TipoAlerta;

class AlertaController extends Controller
{
    /**
     * Mostrar vista de alertas
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
