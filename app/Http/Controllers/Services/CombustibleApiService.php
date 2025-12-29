<?php


namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;

class CombustibleApiService
{
    public function obtenerPrecioActual(): ?float
    {
        /** @var Response $response */
        $response = Http::withToken(config('services.alphacast.key'))
            ->get(config('services.alphacast.url') . '/datasets/38835/latest');

        if ($response->failed()) {
            Log::error('Error API combustible', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return null;
        }

        return (float) ($response->json('data.price'));
    }
}
