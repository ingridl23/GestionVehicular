<?php

namespace App\Services;

use App\Models\Viaje;
use App\Models\PrecioCombustible;
use App\Models\Gasto;
use App\Services\CalculoGastoService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class GastoService
{
    public function __construct(
        protected CombustibleApiService $combustibleApi,
        protected CalculoGastoService $calculoService
    ) {}

    /**
     *  SOLO cálculo (preview / simulación)
     */
    public function calcularGastoPorViaje(int $viajeId): float
    {
        $viaje = Viaje::findOrFail($viajeId);

        $precioLitro = PrecioCombustible::where('fecha', Carbon::today())
            ->value('precio_litro')
            ?? $this->combustibleApi->obtenerPrecioActual();

        if (!$precioLitro) {
            throw new Exception('No hay precio de combustible disponible');
        }

        return $this->calculoService->calcular(
            $viaje->kilometros,
            $precioLitro
        );
    }

    /**
     *  Calcula y GUARDA
     */
    public function generarGastoPorViaje(int $viajeId): Gasto
    {
        return DB::transaction(function () use ($viajeId) {

            $viaje = Viaje::findOrFail($viajeId);

            //  VALIDACIONES DE NEGOCIO (ACÁ)

            if (!$viaje->fecha_fin) {
                throw new Exception('El viaje no está finalizado');
            }

            if (!$viaje->kilometros) {
                throw new Exception('El viaje no tiene kilómetros registrados');
            }

            if (Gasto::where('id_viaje', $viajeId)->exists()) {
                throw new Exception('El viaje ya tiene un gasto asociado');
            }

            // CÁLCULO
            $monto = $this->calcularGastoPorViaje($viajeId);

            //  PERSISTENCIA
            return Gasto::create([
                'id_viaje'         => $viaje->id,
                'kilometros'       => $viaje->kilometros,
                'id_estados_nafta' => $viaje->id_estado_nafta,
                'monto'            => $monto,
            ]);
        });
    }
}
