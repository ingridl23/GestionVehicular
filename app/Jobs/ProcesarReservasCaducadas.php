<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\reservas\BaseReservasServices;
class ProcesarReservasCaducadas implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
     public function handle(BaseReservasServices $service): void
    {
        $service->procesarReservasCaducadas();
    }
}
