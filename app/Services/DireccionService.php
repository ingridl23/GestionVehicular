<?php

namespace App\Services;
use App\Models\Direcciones;

/**
 * @brief Clase service de direcciones de oficinas registradas en el sistema
 */
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
