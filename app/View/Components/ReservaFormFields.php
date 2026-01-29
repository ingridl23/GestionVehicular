<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ReservaFormFields extends Component
{

    public $reserva;
    public $vehiculos;
    public $usuarios;
    public $ubicacion;

    /**
     * Create a new component instance.
     */
    public function __construct($reserva = null, $vehiculos, $usuarios, $ubicacion = null)
    {
        $this->reserva = $reserva;
        $this->vehiculos = $vehiculos;
        $this->usuarios = $usuarios;
        $this->ubicacion = $ubicacion;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.reserva-form-fields');
    }
}
