<?php

namespace App\Observers;

use App\Jobs\SendAccountDeletedNotification;
use App\Jobs\SendEmailVerificationNotification;
use App\Jobs\SendPasswordUpdatedNotification;
use App\Jobs\SendUserRegisteredNotification;
use App\Models\EmailVerify;
use App\Models\User;
use Illuminate\Support\Str;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        //
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        if ($user->wasChanged('password')) {
            // Despachar el job para enviar la notificación
            SendPasswordUpdatedNotification::dispatch($user);
        }

        // Verificar si el campo 'email_verified_at' cambió y no era previamente verificado
        if ($user->wasChanged('email_verified_at') && !is_null($user->email_verified_at)) {
            // Despachar el Job para enviar la notificación de registro exitoso
            SendUserRegisteredNotification::dispatch($user);
        }
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        SendAccountDeletedNotification::dispatch($user);
    }

    /**
     * Handle the User "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
