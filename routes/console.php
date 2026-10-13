<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Prune old tracking logs daily (PII hygiene, keeps tracking_logs small)
Schedule::command('tracking:prune-logs --days=90')->daily();
