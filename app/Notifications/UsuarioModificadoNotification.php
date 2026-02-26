<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UsuarioModificadoNotification extends Notification
{
    use Queueable;

     public function __construct(
        public string $mensaje,
        public string $tipo = 'info'
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['database']; // guardado en BD
    }

    public function toDatabase($notifiable): array
    {
        return [
            'mensaje' => $this->mensaje,
            'tipo' => $this->tipo,
        ];
    }




    /*
    public function via(object $notifiable): array
    {
        return ['mail'];
    }
*/
    /**
     * Get the mail representation of the notification.

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

*/


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
