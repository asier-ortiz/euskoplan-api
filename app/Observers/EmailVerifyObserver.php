<?php

namespace App\Observers;

use App\Jobs\SendEmailVerificationNotification;
use App\Models\EmailVerify;

class EmailVerifyObserver
{
    /**
     * Handle the EmailVerify "created" event.
     *
     * @param  \App\Models\EmailVerify  $emailVerify
     * @return void
     */
    public function created(EmailVerify $emailVerify)
    {
        // Despacha el job para enviar la notificación de verificación de email
        SendEmailVerificationNotification::dispatch($emailVerify->user, $emailVerify->token);
    }

    /**
     * Handle the EmailVerify "updated" event.
     *
     * @param  \App\Models\EmailVerify  $emailVerify
     * @return void
     */
    public function updated(EmailVerify $emailVerify)
    {
        if ($emailVerify->wasChanged('token')) {
            // Despacha el job para enviar la notificación de verificación de email cuando cambie el token
            SendEmailVerificationNotification::dispatch($emailVerify->user, $emailVerify->token);
        }
    }

    /**
     * Handle the EmailVerify "deleted" event.
     *
     * @param  \App\Models\EmailVerify  $emailVerify
     * @return void
     */
    public function deleted(EmailVerify $emailVerify)
    {
        //
    }

    /**
     * Handle the EmailVerify "restored" event.
     *
     * @param  \App\Models\EmailVerify  $emailVerify
     * @return void
     */
    public function restored(EmailVerify $emailVerify)
    {
        //
    }

    /**
     * Handle the EmailVerify "force deleted" event.
     *
     * @param  \App\Models\EmailVerify  $emailVerify
     * @return void
     */
    public function forceDeleted(EmailVerify $emailVerify)
    {
        //
    }
}
