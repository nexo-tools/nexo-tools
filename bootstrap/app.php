<?php

use App\Http\Middleware\NexoNoStore;
use App\Http\Middleware\NexoSsoSessionGuard;
use App\Http\Middleware\NexoSsoSilentLogin;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetLocale;
use App\Services\NexoSso\NexoAuthMode;
use App\Services\NexoSso\NexoSsoStaleLogout;
use App\Support\OperatorAlerts;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
            // Nexo ID controls the session (nexo-sso-client v2): back-channel
            // revocations, the periodic prompt=none re-validation and, in sso
            // mode, closing a session Nexo ID doesn't govern. Then the silent
            // SSO trigger for guests. Both pass-through unless NEXO_SSO_ENABLED.
            NexoSsoSessionGuard::class,
            NexoSsoSilentLogin::class,
            // no-store on everything with a session and on the auth screens, so
            // the back button never shows a signed-out person a private page.
            NexoNoStore::class,
        ]);

        // Shared preference cookies (theme + language) are scoped to the parent
        // domain so they cross every ecosystem tool. Each tool has its own APP_KEY,
        // so they must stay UNencrypted to be readable across tools.
        $middleware->encryptCookies(except: ['nexo-lang', 'nexo-theme']);

        // Where `auth` sends a guest. sso mode (NEXO_AUTH_MODE=sso): a deep link
        // goes through Nexo ID and comes back to itself, never showing /login;
        // a guest's «Cerrar sesión» goes to Nexo ID's hint-less end_session.
        // Otherwise the login page, as before.
        $middleware->redirectGuestsTo(fn (Request $request): string => NexoAuthMode::guestRedirectUrl($request));

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
        // Something broke and nobody is watching: this ecosystem has no error
        // tracker by design (a third party observing users contradicts the
        // product), so the operator hears about it by mail — outside a queued
        // job, every reported exception; inside one, only once the job gives up
        // (templates/nexo-ops, OperatorAlerts). Deduped by exception identity
        // for 15 minutes, off unless NEXO_OPS_MAIL.
        $exceptions->report(fn (Throwable $e) => OperatorAlerts::report($e));
        // «Cerrar sesión» from a page whose session expired (419): no "Page
        // expired" — Nexo ID's end_session without a hint, which asks before
        // ending anything (nexo-sso-client v2.1). Any other 419 is untouched.
        $exceptions->render(fn (HttpException $e, Request $request) => NexoSsoStaleLogout::render($e, $request));
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })
    // The queue half of OperatorAlerts: remembers each failed attempt of a job
    // and mails once the job gives up. Here, not inside withExceptions: that
    // callback runs only once the handler is resolved, and these listeners
    // have to exist before the first job runs (templates/nexo-ops).
    ->booted(fn (Application $app) => OperatorAlerts::listen($app['events']))
    ->create();
