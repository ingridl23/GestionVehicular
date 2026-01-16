<?php

namespace App\Services;
use App\Models\Direcciones;


class DireccionService{

    public function crearDireccion($calle, $altura, $ciudad){
        $direccion = Direcciones::create([
            'calle' => $calle,
            'altura' => $altura,
            'ciudad' => $ciudad
        ]);
        return $direccion->id;
    }

}