<?php
namespace App\Services;

use App\Integrations\Gestya\GPStrack;
use App\Models\Vehiculo;
/**
 * @brief Clase service para la logica d eimplementacion de GPS externo
 */
class GpsTrackingService
{
    protected $provider;

    public function __construct(GPStrack $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Obtiene la última ubicación del vehículo
     */
    public function obtenerUltimaUbicacion(Vehiculo $vehiculo): ?array
    {
        if (!$vehiculo->control_satelital) {
            return null;
        }

        return $this->provider->getLocation($vehiculo->dominio);
    }
}
