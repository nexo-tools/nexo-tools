<?php

use Illuminate\Support\Facades\Schedule;

/*
 * Shared hosting cannot run a long-lived queue worker (no daemons), so the
 * scheduler drains the database queue in short bursts. Needs the standard cron:
 *
 *     * * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
 *
 * This app's mail became queued on 2026-08-02 (family rule C2), so without this
 * line no verification, reset or security notice ever leaves the server — the
 * failure mode that hit nexoid, where the app looked perfectly healthy while
 * every security notice sat in the jobs table.
 */
Schedule::command('queue:work --stop-when-empty --tries=3 --max-time=55')
    ->everyMinute()
    ->withoutOverlapping();
