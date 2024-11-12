<?php

namespace App\Listeners;

use App\Traits\LogActivity;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    use LogActivity;
    /**
     * Menangani event login berhasil.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $this->logActivity('Login Berhasil');
    }
}
