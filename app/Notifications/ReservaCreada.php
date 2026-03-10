<?php
namespace App\Notifications;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ReservaCreada extends Notification
{
    use Queueable;

    protected $reserva;

    public function __construct(Reserva $reserva)
    {
        $this->reserva = $reserva;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nueva reserva de vehículo')
            ->greeting('Sistema de Gestión Vehicular')
            ->line('Se ha registrado una nueva reserva.')
            ->line('Vehículo: ' . $this->reserva->vehiculo->dominio)
            ->line('Fecha inicio: ' . $this->reserva->fecha_inicio_reserva)
            ->line('Fecha fin: ' . $this->reserva->fecha_fin_reserva)
            ->action('Ver reserva', url('/reservas/'.$this->reserva->id))
            ->line('Municipalidad de Tres Arroyos');
    }

    public function toArray($notifiable)
    {
        return [
            'mensaje' => 'Se registró una nueva reserva',
            'reserva_id' => $this->reserva->id
        ];
    }
}
