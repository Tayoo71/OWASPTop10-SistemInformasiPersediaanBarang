<?php

namespace App\Listeners;

use App\Traits\LogActivity;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogSuccessfulLogout
{
    use LogActivity;
    /**
     * Menangani Log Logout berhasil.
     */
    public function handle(Logout $event): void
    {
        $this->logActivity('Logout Berhasil');
    }
}
