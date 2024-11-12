<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Schedule::command('activitylog:clean --force')->daily()
    ->onFailure(function () {
        Log::error('Failed to Clean Up Activity Logs');
    });

Schedule::command('enlightn --report')->runInBackground()->daily()
    ->onFailure(function () {
        Log::error('Failed to Run Enlightn');
    });
