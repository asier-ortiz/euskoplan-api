<?php

namespace App\Observers;

use App\Jobs\SendPasswordResetRequestNotification;
use App\Jobs\SendPasswordResetSuccessNotification;
use App\Models\PasswordReset;
use App\Models\User;

class PasswordResetObserver
{
    /**
     * Handle the PasswordReset "created" event.
     *
     * @param PasswordReset $passwordReset
     * @return void
     */
    public function created(PasswordReset $passwordReset)
    {
        $user = User::where('email', $passwordReset->email)->first();
        if ($user) {
            // Despachar el job para enviar la notificación de restablecimiento de contraseña
            SendPasswordResetRequestNotification::dispatch($user, $passwordReset->token);
        }
    }

    /**
     * Handle the PasswordReset "updated" event.
     *
     * @param PasswordReset $passwordReset
     * @return void
     */
    public function updated(PasswordReset $passwordReset)
    {
        //
    }

    /**
     * Handle the PasswordReset "deleted" event.
     *
     * @param PasswordReset $passwordReset
     * @return void
     */
    public function deleted(PasswordReset $passwordReset)
    {
        $user = User::where('email', $passwordReset->email)->first();
        if ($user) {
            // Despachar el job para enviar la notificación de éxito en el restablecimiento
            SendPasswordResetSuccessNotification::dispatch($user);
        }
    }

    /**
     * Handle the PasswordReset "restored" event.
     *
     * @param PasswordReset $passwordReset
     * @return void
     */
    public function restored(PasswordReset $passwordReset)
    {
        //
    }

    /**
     * Handle the PasswordReset "force deleted" event.
     *
     * @param PasswordReset $passwordReset
     * @return void
     */
    public function forceDeleted(PasswordReset $passwordReset)
    {
        //
    }
}
