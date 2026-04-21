<?php
namespace App\Services\alertas;
use App\Models\Reserva;
use App\Enums\TipoAlerta;
use App\Models\EstadosReserva;


/**
 * @brief AlertaReservaService dedicado a las alertas de reservas dentro del sistema
 * @description AlertaReservaService permite validar el estado de reservas
 * permitiendo que los usuarios sepan estado de la reservacion , fecha de venciminto y recibir el aviso de manera automatica
 * o meidante una notificacion.
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


            $horasRestantes = now()->diffInHours($reserva->fecha_fin_reserva);
            // RESERVA POR VENCER
            if (
               $horasRestantes <= $this->horasDeAviso
                && $reserva->fecha_fin_reserva > now()){
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
