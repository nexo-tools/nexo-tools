<?php

use App\Http\Controllers\BeaconController;
use App\Http\Middleware\BeaconCors;
use Illuminate\Support\Facades\Route;

// Cookieless beacon ingestion. Registered OUTSIDE the web group on purpose: no
// session (so no Set-Cookie, AC-BEACON-7), no CSRF (sendBeacon is cross-origin).
// CORS + preflight live in BeaconCors; the per-IP limit in the `beacon` limiter.
Route::match(['POST', 'OPTIONS'], '/beacon', BeaconController::class)
    ->middleware([BeaconCors::class, 'throttle:beacon'])
    ->name('beacon');
