<?php

namespace App\Listeners;

use App\Traits\LogActivity;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    use LogActivity;
    /**
     * Menangani Log login berhasil.
     */
    public function handle(Login $event)
    {
        $this->logActivity('Login Berhasil');
    }
}
