<?php
namespace App\Integrations\GPS\Contracts;
use App\Models\Vehiculo;
use App\Services\AlertaService;

class GpsSyncService
{
    public function __construct(
        private GpsProviderInterface $provider,
        private AlertaService $alertaService
    ) {}

    public function sincronizar(): void
    {
        $alertas = $this->provider->obtenerAlertas();

        foreach ($alertas as $alertaGps) {

            $vehiculo = Vehiculo::where(
                'gestya_device_id',
                $alertaGps['device_id']
            )->first();

            if (!$vehiculo) continue;

            $this->alertaService->crearSiNoExiste(
                'gps_evento',
                'vehiculo',
                $vehiculo->id,
                $alertaGps['mensaje'],
                'warning'
            );
        }
    }
}
