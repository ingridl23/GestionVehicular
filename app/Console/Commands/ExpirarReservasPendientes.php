<?php

namespace App\Console\Commands;

use App\Models\EstadosReserva;
use App\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpirarReservasPendientes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservas:expirar-pendientes';
    


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expira reservas pendientes luego de 48 horas';

    /**
     * Execute the console command.
     */
    public function handle(){
        $reservas = Reserva::where('estado', 'PENDIENTE')->get();

        foreach ($reservas as $reserva) {
            //addBusinessDays le suma dos dias laborables (de L a V) a la fecha_reserva de la reserva
            //Si una reserva cae viernes, se saltea el sabado y domingo y empieza a sumar a partir del lunes (la del viernes venceria el martes o la del jueves el lunes)
            $vence = Carbon::parse($reserva->fecha_reserva)->addBusinessDays(2);
            
            // Si now es mayor o igual a la fecha donde deberia vencer la reserva
            if (now()->greaterThanOrEqualTo($vence)) {
                $reserva->update([
                    'id_estado_reserva' => EstadosReserva::RECHAZADA
                ]);
            }
        }
    }
}
