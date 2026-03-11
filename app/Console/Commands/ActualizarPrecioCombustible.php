<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Services\CombustibleApiService;
use App\Models\PrecioCombustible;
use Carbon\Carbon;

class ActualizarPrecioCombustible extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'combustible:actualizar-precio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza el precio diario del combustible desde API externa';

    /**
     * Execute the console command.
     */
    public function handle(CombustibleApiService $api)
    {
        $precio = $api->obtenerPrecioActual();

        if (!$precio) {
            $this->error('No se pudo obtener el precio del combustible');
            return Command::FAILURE;
        }

        PrecioCombustible::updateOrCreate(
            ['fecha' => Carbon::today()],
            ['precio_litro' => $precio]
        );

        $this->info("Precio actualizado: $precio");
        return Command::SUCCESS;
    }
}
