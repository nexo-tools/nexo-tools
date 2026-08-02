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

// v2 scaffolding: accounts (+ Nexo ID SSO by env) for "your tools". Unused by v1.
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store'])->middleware('throttle:10,1');

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:20,1');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:5,1')->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->middleware('throttle:5,1')->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // "Your tools" springboard (v2 dashboard): launch + curate ecosystem tools.
    Route::get('app', [SpringboardController::class, 'index'])->name('dashboard');
    Route::post('app/tools', [SpringboardController::class, 'store'])->name('app.tools.store');
    Route::delete('app/tools/{tool}', [SpringboardController::class, 'destroy'])->name('app.tools.destroy');
});

// Ecosystem metrics — gated by the Nexo ID sub allowlist (nexo.admin_subs).
// The gate 403s everyone else; with no admin_subs there is no admin surface.
Route::middleware(EnsureNexoAdmin::class)->group(function () {
    Route::get('admin', AdminDashboardController::class)->name('admin.dashboard');
});

// Nexo ID SSO client (no-op unless NEXO_SSO_ENABLED) — powers v2 "your tools".
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
