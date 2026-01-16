<?php

namespace App\Services;

use App\Models\Alerta;
use Carbon\Carbon;

class AlertaService
{
    public function crearSiNoExiste(
        string $tipo,
        string $entidadTipo,
        int $entidadId,
        string $mensaje,
        string $nivel = 'warning'
    ): void {
        Alerta::firstOrCreate(
            [
                'tipo' => $tipo,
                'entidad_tipo' => $entidadTipo,
                'entidad_id' => $entidadId,
                'activa' => true,
            ],
            [
                'mensaje' => $mensaje,
                'nivel' => $nivel,
                'fecha_generada' => now(),
            ]
        );
    }

    public function resolver(
        string $tipo,
        string $entidadTipo,
        int $entidadId
    ): void {
        Alerta::where([
            'tipo' => $tipo,
            'entidad_tipo' => $entidadTipo,
            'entidad_id' => $entidadId,
            'activa' => true
        ])->update([
            'activa' => false,
            'fecha_resuelta' => now()
        ]);
    }
}
