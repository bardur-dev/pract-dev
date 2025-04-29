<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

$schedule = app(Schedule::class);

$schedule->command('tasks:daily-report')
    ->dailyAt('10:00')
    ->timezone('Europe/Moscow')
    ->description('Daily tasks PDF reports');
