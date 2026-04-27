<?php
namespace App\Services\alertas;
use App\Models\Reserva;
use App\Enums\TipoAlerta;
use App\Models\EstadosReserva;


/**
 * @brief AlertaReservaService dedicado a las alertas de reservas dentro del sistema
 * @description AlertaReservaService permite validar el estado de reservas
 * permitiendo que los usuarios sepan estado de la reservacion , fecha de venciminto y recibir el aviso de manera automatica
 * o mediante una notificacion.
 */

class AlertaReservaService
{
    protected int $horasDeAviso = 24; // aviso 1 día antes

    public function verificar(): void
    {
        $reservas = Reserva::with(['vehiculo', 'usuario'])
            ->whereNotNull('fecha_fin_reserva')
            ->get();

        foreach ($reservas as $reserva) {

           $minutosRestantes = now()->diffInMinutes($reserva->fecha_fin_reserva, false);

    // RESERVA VENCIDA
    if (
        $minutosRestantes < 0 &&
        !in_array($reserva->id_estado_reserva, [
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



          //  RESERVA POR VENCER (ej: 24h = 1440 min)
    if (
        $minutosRestantes > 0 &&
        $minutosRestantes <= ($this->horasDeAviso * 60)
    ) {
        app(AlertaService::class)->crearSiNoExiste(
            TipoAlerta::RESERVA_POR_VENCER,
            'reserva',
            $reserva->id,
            "La reserva del vehículo {$reserva->vehiculo->dominio} está por vencer",
            'warning'
        );
    } else {
        app(AlertaService::class)->resolver(
            TipoAlerta::RESERVA_POR_VENCER,
            'reserva',
            $reserva->id
        );
    }
}
    }
}
