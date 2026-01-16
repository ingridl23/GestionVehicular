<?php

namespace App\Services;

use App\Models\Carnet;
use App\Enums\TipoAlerta;

class AlertaLicenciaService
{
    private int $diasAviso = 30;

    public function verificar(): void
    {
        $carnets = Carnet::with('user')->get();

        foreach ($carnets as $carnet) {

            $usuario = $carnet->user;

            //  Licencia vencida
            if ($carnet->fecha_vencimiento < now()) {

                $carnet->update(['vigente' => false]);

                app(AlertaService::class)->crearSiNoExiste(
                    TipoAlerta::LICENCIA_VENCIDA,
                    'usuario',
                    $usuario->id,
                    "La licencia de {$usuario->name} está vencida",
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
                    "La licencia de {$usuario->name} vence pronto",
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
