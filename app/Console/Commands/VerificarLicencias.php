<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\alertas\AlertaLicenciaService;

class VerificarLicencias extends Command
{
    protected $signature = 'alertas:verificar-licencias';

    protected $description = 'Verifica vencimiento de licencias de conducir y genera alertas';

    public function handle(AlertaLicenciaService $service): int
    {
        $service->verificar();

        $this->info('Verificación de licencias ejecutada correctamente.');

        return Command::SUCCESS;
    }
}
