<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('api:archive-logs')
    ->weekly()
    ->runInBackground()
    ->onFailure(function () {
        Log::warning('api:archive-logs command failed');
    });
