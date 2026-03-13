<?php
namespace App\Services;

use App\Models\Gasto;
use App\Models\Viaje;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * Service de  calculo estadistico de gasto de ombustible y kilometros recorridos
 */
class CalculoGastoService
{
    public function calcularMonto(float $litrosConsumidos, float $precioLitro): float
    {
        return round($litrosConsumidos * $precioLitro, 2);
    }

}
