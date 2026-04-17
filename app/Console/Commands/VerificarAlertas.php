<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\alertas\AlertaVTVService;
use App\Services\alertas\AlertaLicenciaService;
use App\Services\alertas\AlertaReservaService;
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
   AlertaVTVService $vtvService,
   AlertaLicenciaService $licenciaService,
   AlertaReservaService $reserva
    ) {
        $vtvService->verificar();
        $licenciaService->verificar();
        $reserva->verificar();
    }
}
