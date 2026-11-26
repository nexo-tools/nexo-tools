<?php

use App\Support\NexoChrome;
use Illuminate\Support\Facades\Route;

// Guardian (chrome v2, 2026-11-26): with SSO on, «Continuar con Nexo ID» is the
// first and only primary action of the sign-in screen, and the password form
// follows NEXO_AUTH_MODE (CONTRATO-F3 §2.7, §5.4):
//
//   local       Nexo ID first, then the password form
//   transition  Nexo ID first, the password form folded, no «Recordarme»
//   sso         Nexo ID only: no password field on the page
//
// Why it exists: four of the five tools put the local form first and Nexo ID
// last, under an «o» (H-cruce-08). People typed their Nexo ID password into a
// form that could never accept it, got «credenciales no coinciden» and no hint.
//
// It checks the rendered screen, not a component: the pattern ships both as
// nexo-ui's <x-nexo-sign-in-options> and as the sso-client's <x-nexo-sso.login>,
// and an app renders one of them. Copy into tests/Feature/Nexo/. Nothing to
// adapt: an app without an SSO client (Nexo ID itself) skips.

function loginNexoIdFirstPage(): ?string
{
    if (! Route::has('login')) {
        return null;
    }

    $response = test()->get(route('login'));

    return $response->status() === 200 ? $response->getContent() : null;
}

/** Offset of the «Continuar con Nexo ID» anchor (href = the interactive authorize), or false. */
function loginNexoIdFirstSsoAnchor(string $html, ?array &$tag = null)
{
    $href = preg_quote('href="'.NexoChrome::ssoSignInUrl().'"', '/');
    if (! preg_match('/<a\b[^>]*'.$href.'[^>]*>/s', $html, $m, PREG_OFFSET_CAPTURE)) {
        return false;
    }
    $tag = $m[0][0];

    return $m[0][1];
}

function loginNexoIdFirstSkipWithoutSso(): void
{
    if (NexoChrome::ssoSignInUrl() === null) {
        test()->markTestSkipped('No SSO client in this app (Nexo ID is the IdP, or NEXO_SSO_ENABLED=false at boot).');
    }
}

it('puts «Continuar con Nexo ID» first, as the only primary action', function () {
    loginNexoIdFirstSkipWithoutSso();

    $html = loginNexoIdFirstPage();
    if ($html === null) {
        test()->markTestSkipped('The login screen does not render in this mode.');
    }

    $sso = loginNexoIdFirstSsoAnchor($html, $tag);
    expect($sso)->not->toBeFalse('The login screen has no «Continuar con Nexo ID» (x-nexo-sign-in-options or x-nexo-sso.login).');

    $password = strpos($html, 'type="password"');
    if ($password !== false) {
        expect($sso)->toBeLessThan($password, '«Continuar con Nexo ID» must come before the password form.');
    }

    expect(str_contains((string) $tag, 'nexo-btn--primary'))->toBeTrue('«Continuar con Nexo ID» must be the primary button.');

    // One violet button per screen: the local submit steps down to ghost.
    $card = substr($html, (int) strpos($html, 'data-nexo-auth-card'));
    $card = substr($card, 0, (int) (strpos($card, '</main>') ?: strlen($card)));
    expect(substr_count($card, 'nexo-btn--primary'))->toBe(1, 'The sign-in card has more than one primary button.');
});

it('shapes the password form after the auth mode', function () {
    loginNexoIdFirstSkipWithoutSso();

    $html = loginNexoIdFirstPage();
    $mode = NexoChrome::authMode();

    if ($mode === 'sso') {
        if ($html !== null) {
            expect(str_contains($html, 'type="password"'))->toBeFalse('In sso mode the sign-in screen must not ask for a password.');
        }
        expect(NexoChrome::signUpUrl())->toBe(NexoChrome::issuer().'/register', 'In sso mode «Crear cuenta» is Nexo ID\'s sign-up.');

        return;
    }

    expect($html)->not->toBeNull('The login screen must render outside sso mode.');

    if ($mode === 'transition') {
        $folded = preg_match_all('/<details\b.*?<\/details>/s', $html, $folds) > 0
            && str_contains(implode('', $folds[0]), 'type="password"');
        expect($folded)->toBeTrue('In transition the password form is folded (<details>) under «Usar email y contraseña».');
        expect(str_contains($html, 'name="remember"'))->toBeFalse('In transition the tool does not offer its own «Recordarme».');

        return;
    }

    expect(str_contains($html, 'type="password"'))->toBeTrue('In local mode the password form stays.');
});

it('keeps the plain password form when SSO is switched off', function () {
    if (loginNexoIdFirstPage() === null) {
        test()->markTestSkipped('This app has no login screen.');
    }

    config()->set('nexo-sso.enabled', false);
    $html = (string) loginNexoIdFirstPage();

    expect(str_contains($html, '/auth/nexo/'))->toBeFalse('Standalone, there is no Nexo ID button.');
    expect(str_contains($html, 'type="password"'))->toBeTrue('Standalone, the password form is the way in.');
});
