<?php

namespace App\Services;

use App\Models\Reserva;
use App\Enums\TipoAlerta;
use App\Models\EstadosReserva;

class AlertaReservaService
{
    private int $diasAviso = 1; // aviso 1 día antes

    public function verificar(): void
    {
        $reservas = Reserva::with(['vehiculo', 'usuario'])
            ->whereNotNull('fecha_fin_reserva')
            ->get();

        foreach ($reservas as $reserva) {

            //  RESERVA VENCIDA
            if (
                $reserva->fecha_fin_reserva < now()
                && !in_array($reserva->id_estado_reserva, [
                    EstadosReserva::FINALIZADA,
                    EstadosReserva::CANCELADA
                ])
            ) {
                app(AlertaService::class)->crearSiNoExiste(
                    TipoAlerta::RESERVA_VENCIDA,
                    'reserva',
                    $reserva->id,
                    "La reserva del vehículo {$reserva->vehiculo->dominio} está vencida",
                    'critica'
                );

                continue;
            }

            // RESERVA POR VENCER
            if (
                $reserva->fecha_fin_reserva
                ->diffInHours(now()) <= 24
                && $reserva->fecha_fin_reserva > now()
            ) {
                app(AlertaService::class)->crearSiNoExiste(
                    TipoAlerta::RESERVA_POR_VENCER,
                    'reserva',
                    $reserva->id,
                    "La reserva del vehículo {$reserva->vehiculo->dominio} está por vencer",
                    'warning'
                );
            } else {
                // limpiar alertas si ya no aplica
                app(AlertaService::class)->resolver(
                    TipoAlerta::RESERVA_POR_VENCER,
                    'reserva',
                    $reserva->id
                );
            }
        }
    }
}
