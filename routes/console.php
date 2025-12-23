<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the ensure-users-have-teams command to run every 15 minutes
Schedule::command('app:ensure-users-have-teams')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground();

// Schedule the Public Suffix List download to run daily at 2 AM
Schedule::command('psl:download')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground();

// Schedule domain calculation to run after PSL download (at 2:05 AM)
Schedule::command('websites:calculate-domains --all')
    ->dailyAt('02:05')
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground();

// Schedule GSC token refresh to run every minute
Schedule::command('gsc:tokens:refresh')
    ->everyMinute()
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground();
