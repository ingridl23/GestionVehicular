<?php

namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * @brief Clase para notificar Usuarios sobre acciones
 * Descripcion: Clase desarrolada para la notificacion mediante email y campana de notificaicon a usuarios registrados en el sistema.
 * Garantizando una comunicacion eficiente entre usuarios y tiempo de respuesta ante eventos del sistema.

*/

class UsuarioModificadoNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     */
     public function __construct(
        public string $mensaje,
        public string $tipo = 'info'
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     *
     */
    public function via($notifiable)
    {
        return ['database','mail']; // guardado en BD
    }

    /**
     * Get the mail representation of the notification.
     *
     */

    public function toDatabase($notifiable): array
    {
        return [
            'mensaje' => $this->mensaje,
            'tipo' => $this->tipo,
             'fecha' => now()->format('d/m H:i')
        ];
    }


    /**
     * Get the mail representation of the notification.
     *
     */
    public function toMail($notifiable)
{
     //   Log::info('Entró a toMail de UsuarioModificadoNotification');
    return (new MailMessage)
        ->subject('Sistema de Gestión Vehicular')
        ->greeting('Hola '.$notifiable->name)
        ->line($this->mensaje)
        ->action('Ingresar al sistema', url('/login'))
        ->line('Municipalidad de Tres Arroyos');
}




}
