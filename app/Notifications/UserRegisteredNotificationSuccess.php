<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRegisteredNotificationSuccess extends Notification
{
    use Queueable;

    protected string $username;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($username)
    {
        $this->username = $username;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $frontendUrl = env('FRONTEND_URL');
        $frontendPort = env('FRONTEND_PORT');

        $url = $frontendUrl . ':' . $frontendPort;

        return (new MailMessage)
            ->subject('¡Bienvenido a nuestra aplicación!')
            ->greeting('¡Hola ' . $this->username . '!')
            ->line('Gracias por registrarte en nuestra aplicación. Tu cuenta ha sido creada exitosamente.')
            ->action('Visítanos', url($url))
            ->salutation('¡Esperamos que disfrutes usando nuestra aplicación!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
