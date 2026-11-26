<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\App\SpringboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegalController;
use App\Http\Middleware\EnsureLocalAuth;
use App\Http\Middleware\EnsureNexoAdmin;
use Illuminate\Support\Facades\Route;

// v1: public ecosystem hub, no account needed.
Route::get('/', HomeController::class)->name('home');

// Help center (translatable FAQs). Public, no account needed.
Route::get('/help', HelpController::class)->name('help');

// Legal pages. The hub holds accounts, the "your tools" panel and the beacon
// table, so it has to say what it stores. Paths are Spanish (the ecosystem is
// Spanish-first); the route NAMES are the ecosystem-wide contract the footer,
// the sitemap and StaticPagesTest reference. Outside every auth group: they must
// be readable while logged out and indexable.
Route::get('/privacidad', [LegalController::class, 'privacy'])->name('legal.privacy');
Route::get('/terminos', [LegalController::class, 'terms'])->name('legal.terms');

// SEO surface (discovery): robots.txt allows crawling the public hub + help and
// disallows the private/account surface; sitemap.xml lists the indexable pages.
Route::get('/sitemap.xml', function () {
    $body = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
        .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
    foreach ([url('/'), route('help'), route('legal.privacy'), route('legal.terms')] as $loc) {
        $body .= '    <url><loc>'.e($loc).'</loc></url>'."\n";
    }
    $body .= '</urlset>'."\n";

    return response($body, 200, ['Content-Type' => 'application/xml']);
})->name('sitemap');

Route::get('/robots.txt', function () {
    $lines = [
        'User-agent: *',
        'Disallow: /app',
        'Disallow: /admin',
        'Disallow: /login',
        'Disallow: /register',
        'Disallow: /forgot-password',
        'Disallow: /reset-password',
        'Disallow: /beacon',
        '',
        'Sitemap: '.route('sitemap'),
    ];

    return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain']);
})->name('robots');

// Accounts. Which local doors are open depends on the auth mode
// (NEXO_AUTH_MODE, nexo-sso-client): EnsureLocalAuth answers 404 for the local
// password login, registration and reset in `sso` — the hosted hub runs there
// (CONTRATO-F3 D2) — and «Crear cuenta» goes to Nexo ID's sign-up instead. A
// standalone install (SSO off) is always `local`: every door as before.
Route::middleware('guest')->group(function () {
    // The login page stays reachable in every mode: in sso it explains Nexo ID
    // and hosts «Continuar con Nexo ID» (x-nexo-sign-in-options).
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->middleware([EnsureLocalAuth::class, 'throttle:20,1']);

    Route::middleware(EnsureLocalAuth::class.':register')->group(function () {
        Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
        Route::post('register', [RegisteredUserController::class, 'store'])->middleware('throttle:10,1');
    });

    Route::middleware(EnsureLocalAuth::class.':password')->group(function () {
        Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
        Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
            ->middleware('throttle:5,1')->name('password.email');

        Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
        Route::post('reset-password', [NewPasswordController::class, 'store'])
            ->middleware('throttle:5,1')->name('password.store');
    });
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // «Tus herramientas» (D7): the light launcher — every tool of the registry,
    // always, each opening its panel already signed in, plus the account. It
    // used to be a springboard the person had to fill by hand; the user_tools
    // table it wrote stays (no data is dropped), the launcher just no longer
    // depends on it.
    Route::get('app', [SpringboardController::class, 'index'])->name('dashboard');
});

// Ecosystem metrics — gated by the Nexo ID sub allowlist (nexo.admin_subs).
// The gate 403s everyone else; with no admin_subs there is no admin surface.
Route::middleware(EnsureNexoAdmin::class)->group(function () {
    Route::get('admin', AdminDashboardController::class)->name('admin.dashboard');
});

// Nexo ID SSO client (nexo-sso-client v2.1): no routes at all unless
// NEXO_SSO_ENABLED. SetLocale already runs in the `web` group, so the
// signed-out screen speaks the person's language.
require __DIR__.'/nexo-sso.php';

// Email verification. Not enforced as middleware on the panel: somebody who
// just signed up has tools to add, and locking them out over an unread email
// would be our problem, not theirs. What it buys is the way back into an
// account whose address had a typo — which was simply lost before.
Route::middleware('auth')->group(function () {
    Route::get('verify-email', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('verify-email/send', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')->name('verification.send');
});

// Last route, on purpose. An unmatched URL used to 404 BEFORE the `web` group
// ran, so the error page had no session and showed a signed-in person a guest
// header (chrome v2: the header knows who you are on every page, the 404
// included — HeaderEverywhereTest). Inside `web`, the 404 gets the session, the
// locale, the security headers and no-store like any other page.
Route::fallback(fn () => abort(404))->name('fallback');
