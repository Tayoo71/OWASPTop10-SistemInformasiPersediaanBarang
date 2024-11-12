<?php

namespace App\Observers;

use App\Models\Shared\User;
use App\Traits\LogActivity;

class UserObserver
{
    use LogActivity;
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Log Jika Two Factor dilakukan perubahan
        if ($user->wasChanged('two_factor_confirmed_at')) {
            $originalConfirmedAt = $user->getOriginal('two_factor_confirmed_at');
            $currentConfirmedAt = $user->two_factor_confirmed_at;

            if (is_null($originalConfirmedAt) && !is_null($currentConfirmedAt)) {
                $this->logActivity(('Autentikasi dua faktor (2FA) diaktifkan untuk Username:.' . $user->id));
            } elseif (!is_null($originalConfirmedAt) && is_null($currentConfirmedAt)) {
                $this->logActivity(('Autentikasi dua faktor (2FA) dinonaktifkan untuk Username:.' . $user->id));
            }
        }
    }
    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
