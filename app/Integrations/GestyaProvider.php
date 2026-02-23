<?php
namespace App\Integrations\Gps;

use App\Integrations\Gps\Contracts\GpsProviderInterface;

class GestyaProvider implements GpsProviderInterface
{
    public function obtenerVehiculos(): array
    {
        return [];
    }

    public function obtenerPosicion(string $deviceId): ?array
    {
        return null;
    }

    public function obtenerAlertas(): array
    {
        return [];
    }
}
