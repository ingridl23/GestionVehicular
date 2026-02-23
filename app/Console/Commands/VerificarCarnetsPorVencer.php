<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\Alerta;
use App\Models\User;
class VerificarCarnetsPorVencer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:verificar-carnets-por-vencer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $usuarios = User::with('carnet')->get();

foreach ($usuarios as $usuario) {

    if ($usuario->carnetPorVencer()) {

        Alerta::firstOrCreate([
            'tipo' => 'carnet_por_vencer',
            'entidad_tipo' => 'usuario',
            'entidad_id' => $usuario->id,
            'activa' => true
        ], [
            'mensaje' => 'La licencia del usuario está por vencer.',
            'nivel' => 'warning',
            'fecha_generada' => now()
        ]);
    }
}

    }
}
