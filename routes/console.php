<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('activitylog:clean', function () {
    $this->comment('Cleaning up old activity logs...');
    Artisan::call('activitylog:clean');
})->purpose('Clean up old activity logs')->daily();
