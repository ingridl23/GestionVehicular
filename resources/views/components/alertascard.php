<?php
namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Alerta;

class AlertaCard extends Component
{
    public Alerta $alerta;

    public string $color;
    public string $icono;
    public string $titulo;

    public function __construct(Alerta $alerta)
    {
        $this->alerta = $alerta;

        $this->mapearEstilo();
    }

    private function mapearEstilo(): void
    {
        // según tipo de alerta (casos de uso reales)
        switch ($this->alerta->tipo) {
            case 'licencia_vencimiento':
                $this->color = 'yellow';
                $this->icono = 'fa-id-card';
                $this->titulo = 'Advertencia';
                break;

            case 'vehiculo_fuera_servicio':
                $this->color = 'red';
                $this->icono = 'fa-car-crash';
                $this->titulo = 'Urgente';
                break;

            case 'reserva_rechazada':
                $this->color = 'orange';
                $this->icono = 'fa-calendar-times';
                $this->titulo = 'Atención';
                break;

            default:
                $this->color = 'blue';
                $this->icono = 'fa-info-circle';
                $this->titulo = 'Información';
        }
    }

    public function render()
    {
        return view('components.alerta-card');
    }
}
