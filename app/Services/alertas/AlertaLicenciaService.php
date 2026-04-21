<?php
namespace App\Services\alertas;
use App\Models\Carnet;
use App\Enums\TipoAlerta;


/**
 * @brief Service dedicado a las alertas de licencias dentro del sistema
 * @description AlertaLicenciaService permite validar informacion importante de licencias de usuarios como carnet y su fecha de vencimiento
 * permitiendo corroborar si los usuarios estan aptos para realizar una reserva y conducir un vehiculo.
 */
class AlertaLicenciaService
{
    private int $diasAviso = 30;

    public function verificar(): void
    {
        $carnets = Carnet::with('user.dependencia')->get();
        $dependenciaNombre = $usuario->dependencia?->nombre ?? 'Sin dependencia';
        foreach ($carnets as $carnet) {

            $usuario = $carnet->user;

            //  Licencia vencida
            if ($carnet->fecha_vencimiento < now()) {

                $carnet->update(['vigente' => false]);

                app(AlertaService::class)->crearSiNoExiste(
                    TipoAlerta::LICENCIA_VENCIDA,
                    'usuario',
                    $usuario->id,
                    "La licencia de {$usuario->name} {$usuario->lastname}de {$dependenciaNombre} está vencida",
                    'critica'
                );
            }

            //  Licencia por vencer
            elseif (
                $carnet->fecha_vencimiento->diffInDays(now()) <= $this->diasAviso
            ) {

                app(AlertaService::class)->crearSiNoExiste(
                    TipoAlerta::LICENCIA_POR_VENCER,
                    'usuario',
                    $usuario->id,


                    "La licencia de {$usuario->name} {$usuario->lastname} ({$dependenciaNombre}) vence el proximo {$carnet->fecha_vencimiento->format('d/m/Y')}",
                    'warning'
                );
            }

            //  Licencia OK
            else {

                $carnet->update(['vigente' => true]);

                app(AlertaService::class)->resolver(
                    TipoAlerta::LICENCIA_VENCIDA,
                    'usuario',
                    $usuario->id
                );

                app(AlertaService::class)->resolver(
                    TipoAlerta::LICENCIA_POR_VENCER,
                    'usuario',
                    $usuario->id
                );
            }
        }
    }
}
