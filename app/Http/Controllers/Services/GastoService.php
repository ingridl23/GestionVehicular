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
     *  Reglas + DB
     */
    public function generarGastoPorViaje(int $viajeId): Gasto
    {
        return DB::transaction(function () use ($viajeId) {

            $viaje = Viaje::findOrFail($viajeId);

            //  VALIDACIONES DE NEGOCIO

            if (!$viaje->fecha_fin) {
                throw new Exception('El viaje no está finalizado');
            }

            if (!$viaje->kilometros) {
                throw new Exception('El viaje no tiene kilómetros registrados');
            }

            if (!$viaje->combustible_consumido) {
                throw new Exception('El viaje no tiene combustible registrado');
            }

            if (Gasto::where('id_viaje', $viajeId)->exists()) {
                throw new Exception('El viaje ya tiene un gasto asociado');
            }

            // CÁLCULO precio del combustible
            $precioLitro = PrecioCombustible::where('fecha', Carbon::today())
                ->value('precio_litro')
                ?? $this->combustibleApi->obtenerPrecioActual();

            if (!$precioLitro) {
                throw new Exception('No hay precio de combustible disponible');
            }

            //calculo

            $importe = $this->calculoService->calcularMonto(
                $viaje->combustible_consumido,
                $precioLitro
            );

            //Persistencia del gasto del viaje

            return Gasto::create([
                'id_viaje'     => $viaje->id,
                'monto'        => $importe,
                'valor_litro'  => $precioLitro,
                'fecha_calculo' => now(),
            ]);
        });
    }
}
