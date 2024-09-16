<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\UserRegisteredNotificationSuccess;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendUserRegisteredNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->onQueue('emails');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Enviar la notificación de registro exitoso
        $this->user->notify(new UserRegisteredNotificationSuccess($this->user->username));
    }
}
