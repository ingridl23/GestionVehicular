<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class listaReservasMobile extends Component
{
    public $reservas;

    public function __construct($reservas)
    {
        $this->reservas = $reservas;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.lista-reservas-mobile');
    }
}
