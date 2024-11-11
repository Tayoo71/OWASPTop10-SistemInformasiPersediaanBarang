<?php

namespace App\Traits;

use Jenssegers\Agent\Agent;

trait LogActivity
{
    /**
     * Log activity with user device details.
     *
     * @param string $description
     */
    public function logActivity(string $description)
    {
        $ipAddress = request()->ip();
        $userAgent = request()->header('User-Agent');

        $agent = new Agent();
        $agent->setUserAgent($userAgent);
        $browser = $agent->browser();
        $browserVersion = $agent->version($browser);
        $platform = $agent->platform();
        $device = $agent->device();

        activity()
            ->withProperties([
                'device' => 'IP Address: (' . $ipAddress . ') | Perangkat: (' . $platform . ' | ' . $device . ' | ' . $browser . ' ' . $browserVersion . ')'
            ])
            ->log($description);
    }
}
