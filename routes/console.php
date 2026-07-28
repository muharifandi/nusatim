<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Requires the cron job in SPEC.md/deploy notes: `php artisan schedule:run`
// once a minute. Country lookups happen here, never in the request path.
Schedule::command('pageviews:resolve-countries --limit=100')->everyFiveMinutes();
