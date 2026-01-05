<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use Illuminate\Http\JsonResponse;

class AlertaController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Alerta::where('activa', true)
                ->orderBy('fecha_generada', 'desc')
                ->get()
        );
    }

    public function porEntidad(string $tipo, int $id): JsonResponse
    {
        return response()->json(
            Alerta::where('entidad_tipo', $tipo)
                ->where('entidad_id', $id)
                ->where('activa', true)
                ->get()
        );
    }

    public function resolver(int $id): JsonResponse
    {
        $alerta = Alerta::findOrFail($id);

        $alerta->update([
            'activa' => false,
            'fecha_resuelta' => now()
        ]);

        return response()->json([
            'message' => 'Alerta resuelta correctamente'
        ]);
    }
}
