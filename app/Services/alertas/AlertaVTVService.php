<?php
namespace App\Services\alertas;
use App\Models\Vehiculo;
use App\Enums\TipoAlerta;
use Carbon\Carbon;

/**
 * @brief Service dedicado a las alertas de VTV de un vehiculo dentro del sistema.
 * @description AlertaVTVService permite validar el estado de la VTV de vehiculos registrados en el sistema.
 * permitiendo que los usuarios administradores sepan estado de la vtv de un vehiculo, fecha de vencimiento de la misma
 *  y recibir el aviso de manera automatica o mediante una notificacion.
 */
class AlertaVTVService
{
    /**
     * Metodo de verificacion del estado de la vtv de vehiculos registrados.
     */
    public function verificar(): void
    {
        $hoy = Carbon::today();

        $vehiculos = Vehiculo::whereNotNull('vtv')->get();

        foreach ($vehiculos as $vehiculo) {

            $dias = $hoy->diffInDays(Carbon::parse($vehiculo->vtv), false);

            /*VTV vencida*/

            if ($dias <= 0) {
                app(AlertaService::class)->crearSiNoExiste(
                    TipoAlerta::VTV_VENCIDA,
                    'vehiculo',
                    $vehiculo->id,
                    "La VTV del vehículo {$vehiculo->dominio} está vencida",
                    'critica'
                );

                continue;
            }

            /* VTV por vencer (ej: 30 días )*/

            if ($dias <= 30) {
                app(AlertaService::class)->crearSiNoExiste(
                    TipoAlerta::VTV_POR_VENCER,
                    'vehiculo',
                    $vehiculo->id,
                    "La VTV del vehículo {$vehiculo->dominio} vence en {$dias} días",
                    'warning'
                );
            } else {
                /* Si está OK, resolvemos alertas previas*/
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
