<?php
namespace App\Services;

use App\Models\Gasto;
use App\Models\Viaje;
use Illuminate\Support\Facades\DB;
use Exception;

class CalculoGastoService
{
public function calcular(Viaje $viaje, float $precioLitro): Gasto
{
if ($viaje->gasto) {
throw new Exception('El viaje ya tiene un gasto calculado');
}

if (!$viaje->combustible_consumido || !$viaje->kilometros) {
throw new Exception('Faltan datos del viaje');
}

$importe = $viaje->combustible_consumido * $precioLitro;

return DB::transaction(function () use ($viaje, $importe, $precioLitro) {
return Gasto::create([
'id_viaje' => $viaje->id,
'importe' => $importe,
'valor_litro' => $precioLitro,
'fecha_calculo' => now(),
]);
});
}
}
