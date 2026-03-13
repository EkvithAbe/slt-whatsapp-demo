<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('whatsapp:sync --limit=30')->everyMinute();

// Keep contacts list up to date based on the last active mobile numbers
Schedule::command('whatsapp:sync-contacts --limit=5')->everyMinute();

