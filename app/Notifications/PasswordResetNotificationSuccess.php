<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotificationSuccess extends Notification
{
    use Queueable;

    public function __construct()
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Cambio de contraseña')
            ->greeting('¡Saludos!')
            ->line('Tu contraseña ha sido modificada exitosamente')
            ->salutation('¡Gracias por usar nuestra aplicación!');
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
