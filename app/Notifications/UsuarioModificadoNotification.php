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
        return ['database','mail']; // guardado en BD
    }

    public function toDatabase($notifiable): array
    {
        return [
            'mensaje' => $this->mensaje,
            'tipo' => $this->tipo,
             'fecha' => now()->format('d/m H:i')
        ];
    }

public function toMail($notifiable)
{
    return (new MailMessage)
        ->subject('Sistema de Gestión Vehicular')
         ->view('emails.reserva', [
            'mensaje' => $this->mensaje,
            'url' => url('/login'),
            'usuario' => $notifiable->name
        ]);
}





}
