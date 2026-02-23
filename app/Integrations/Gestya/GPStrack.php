<?php
namespace App\Integrations\Gestya;

use Illuminate\Support\Facades\Http;

class GpsTrack
{
    protected string $baseUrl;
    protected string $token;

    public function __construct()
    {
        $this->baseUrl = config('services.gestya.url');
        $this->token = config('services.gestya.token');
    }

    public function getLocation(string $dominio): ?array
    {
        $response = Http::withToken($this->token)
            ->get($this->baseUrl . '/gps/location', [
                'dominio' => $dominio
            ]);

        if (!$response->successful()) {
            return null;
        }

        $data = $response->json();

        return [
            'lat' => $data['latitude'] ?? null,
            'lng' => $data['longitude'] ?? null,
            'direccion' => $data['address'] ?? null,
        ];
    }
}
