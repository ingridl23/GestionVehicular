<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AlertaVTVService;
use App\Services\AlertaLicenciaService;
use App\Services\AlertaReservaService;
class VerificarAlertas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:verificar-alertas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */

    public function handle(
        AlertaVtvService $vtvService,
        AlertaLicenciaService $licenciaService,
         AlertaReservaService $reserva
    ) {
        $vtvService->verificar();
        $licenciaService->verificar();
        $reserva->verificar();
    }
}
