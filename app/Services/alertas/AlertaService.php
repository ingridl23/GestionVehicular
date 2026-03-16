<?php
namespace App\Services;
use App\Models\Alerta;
use App\Models\Reportes;
use Carbon\Carbon;


/**
 * @brief AlertaService dedicado a las alertas dentro del sistema
 * @description AlertaService permite la creacion del contenido de una alerta.
 *
 */
class AlertaService
{


/**
 * Crear alerta si no existe en el sistema
 */
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


/**
 * Resolver la alerta si ha sido mostrada al usuario
 */
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
