<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Per-IP cap for the cookieless beacon (default 60/min). (AC-BEACON-5)
        RateLimiter::for('beacon', fn (Request $request): Limit => Limit::perMinute(
            (int) config('nexo.beacon.rate_limit', 60)
        )->by((string) $request->ip()));
    }
}
