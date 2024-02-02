<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerifyNotificationRequest extends Notification
{
    use Queueable;

    protected string $token;
    protected string $username;

    public function __construct($token, $username)
    {
        $this->token = $token;
        $this->username = $username;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url('http://localhost:4200/auth/email-verify?token=' . $this->token);

        return (new MailMessage)
            ->subject('Verificación de correo electrónico')
            ->greeting('¡Saludos ' . $this->username . '!')
            ->line('Bienvenido a Euskoplan.')
            ->line('Por favor, haz click en el enlace para completar tu registro')
            ->action('Verificar correo electrónico', url($url))
            ->line('')
            ->salutation('¡Gracias por usar nuestra aplicación!');
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
