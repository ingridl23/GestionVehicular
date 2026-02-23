<?php

namespace App\Services;

use App\Models\Vehiculo;
use App\Enums\TipoAlerta;
use Carbon\Carbon;
class AlertaVtvService
{
    public function verificar(): void
    {
        $hoy = Carbon::today();

        $vehiculos = Vehiculo::whereNotNull('vtv')->get();

        foreach ($vehiculos as $vehiculo) {

            $dias = $hoy->diffInDays(Carbon::parse($vehiculo->vtv), false);

            // VTV vencida
            if ($dias < 0) {
                app(AlertaService::class)->crearSiNoExiste(
                    TipoAlerta::VTV_VENCIDA,
                    'vehiculo',
                    $vehiculo->id,
                    "La VTV del vehículo {$vehiculo->dominio} está vencida",
                    'critica'
                );

                continue;
            }

            // VTV por vencer (ej: 30 días)
            if ($dias <= 30) {
                app(AlertaService::class)->crearSiNoExiste(
                    TipoAlerta::VTV_POR_VENCER,
                    'vehiculo',
                    $vehiculo->id,
                    "La VTV del vehículo {$vehiculo->dominio} vence en {$dias} días",
                    'warning'
                );
            } else {
                // Si está OK, resolvemos alertas previas
                app(AlertaService::class)->resolver(
                    TipoAlerta::VTV_POR_VENCER,
                    'vehiculo',
                    $vehiculo->id
                );
                app(AlertaService::class)->resolver(
                    TipoAlerta::VTV_VENCIDA,
                    'vehiculo',
                    $vehiculo->id
                );
            }
        }
    }
}
