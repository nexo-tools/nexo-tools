<?php

use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        // Beacon ingestion is loaded with NO route group, so it inherits neither
        // sessions/CSRF (web) nor the /api prefix — it owns its own middleware.
        then: fn () => Route::group([], base_path('routes/beacon.php')),
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SetLocale::class,
            SecurityHeaders::class,
        ]);

        // Set TRUSTED_PROXIES in production (Cloudflare ranges, or '*' when the
        // origin is reachable ONLY through Cloudflare). Empty in local/dev.
        // Without it, the beacon per-IP rate-limit and VisitorHash collapse to
        // the edge IP behind a CDN.
        if ($proxies = env('TRUSTED_PROXIES')) {
            $middleware->trustProxies(
                at: $proxies === '*' ? '*' : array_map('trim', explode(',', (string) $proxies)),
            );
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
