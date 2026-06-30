<?php
namespace App\Notifications;
use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * @brief Clase para notificar reservas vinculadas a usuarios
 * Descripcion: Clase desarrolada para la notificacion mediante email a usuarios registrados en el sistema.Mediante un mensaje
 *  de correo eletronico personalizado.
 */
class ReservaCreada extends Notification  implements ShouldQueue
{
    use Queueable;

    protected $reserva;


    /**
     * Create a new notification instance.
     *
     */
    public function __construct(Reserva $reserva)
    {
        $this->reserva = $reserva;
    }


    /**
     * Get the notification's delivery channels.
     *
     */
    public function via($notifiable)
    {
        return ['mail'];
    }


    /**
     * Get the mail representation of the notification.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     *
     */


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

    /**
     * Get the array representation of the notification.
     *
     */
    public function toArray($notifiable)
    {
        return [
            'mensaje' => 'Se registró una nueva reserva',
            'reserva_id' => $this->reserva->id
        ];
    }
}
