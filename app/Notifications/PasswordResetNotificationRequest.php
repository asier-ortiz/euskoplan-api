<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotificationRequest extends Notification
{
    use Queueable;

    protected string $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $frontendUrl = env('FRONTEND_URL');
        $frontendPort = env('FRONTEND_PORT');

        $url = $frontendUrl . ':' . $frontendPort . '/auth/password-reset?token=' . $this->token;

        return (new MailMessage)
            ->subject('Solicitud de reinicio de contraseña')
            ->greeting('¡Saludos!')
            ->line('Te enviamos este correo para que puedas restablecer tu contraseña')
            ->action('Reiniciar contraseña', url($url))
            ->line('')
            ->line('Si no habías solicitado este cambio puedes ignorar este email')
            ->salutation('¡Gracias por usar nuestra aplicación!');
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
