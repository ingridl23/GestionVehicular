<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('combustible:actualizar-precio')
            ->dailyAt('06:00')
            ->withoutOverlapping()
            ->onFailure(function () {
                Log::error('Falló la actualización del precio de combustible');
            });

        //  TODAS LAS ALERTAS
        $schedule->command('app:verificar-alertas')
            ->dailyAt('03:00')
            ->withoutOverlapping();
        
        $schedule->command('reservas:expirar-pendientes')->everyThirtyMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}

/**
 *
 * Esto lo dejo para mas adelante
 * Activar scheduler en el servidor (MUY IMPORTANTE)

En el servidor (Linux / producción):

crontab -e


Agregar:

 * * * * * php /ruta/a/tu/proyecto/artisan schedule:run >> /dev/null 2>&1
 */
