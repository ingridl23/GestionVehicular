<?php
namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Alerta;

class AlertaCard extends Component
{
    public Alerta $alerta;

    public function __construct(Alerta $alerta)
    {
        $this->alerta = $alerta;


    }

    public function render()
    {
        return view('components.alerta-card');
    }
}
