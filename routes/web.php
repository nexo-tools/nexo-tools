<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\App\SpringboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\HomeController;
use App\Http\Middleware\EnsureNexoAdmin;
use Illuminate\Support\Facades\Route;

// v1: public ecosystem hub, no account needed.
Route::get('/', HomeController::class)->name('home');

// Help center (translatable FAQs). Public, no account needed.
Route::get('/help', HelpController::class)->name('help');

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
