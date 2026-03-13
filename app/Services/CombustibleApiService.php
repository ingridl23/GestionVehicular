<?php


namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;
/**
 * @brief Clase service de conexion con api de combustible alphacast
 */
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


        $data = $response->json('data');

        if (!$data) {
            return null;
        }

        //activarlo para poder saber visualizarlo en la UI con datos
       //|
      // V
        //dd($response->json());

        // Ejemplo: tomar el precio de nafta SUPER
        return isset($data['nafta_grado_2_super'])
            ? (float) $data['nafta_grado_2_super']
            : null;
    }
}
