<?php
namespace App\Integrations\GPS\Contracts;

interface GpsProviderInterface
{
    public function obtenerVehiculos(): array;

    public function obtenerPosicion(string $deviceId): ?array;

    public function obtenerAlertas(): array;
}
