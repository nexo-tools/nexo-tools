<?php

use App\Support\NexoChrome;

// Guardian (chrome v2, 2026-11-26): the CSP `form-action` lets the sign-in and
// sign-out round trips reach the other side (CONTRATO-F3 §3):
//
//   tools    form-action 'self' <origin of NEXO_SSO_ISSUER>   (only with SSO on)
//   Nexo ID  form-action 'self' <origin of every client tool in the registry>
//
// Why it exists: `form-action 'self'` also governs the redirects that FOLLOW a
// form POST. In the tools it blocked «Cerrar sesión» → Nexo ID's end_session
// (nothing happened on screen and Nexo ID stayed open, in Chrome and Safari);
// in Nexo ID it blocked POST /login → authorize → the tool's callback, so
// signing in from a tool stalled in Chrome (the three severity-4 findings of
// 2026-11-24 were these two).
//
// Production: LiteSpeed REPLACES the app's CSP with the one in
// public/.htaccess, so the .htaccess policy must carry the same origins
// (SecurityHeadersTest keeps both in sync). Copy into tests/Feature/Nexo/.

function cspFormAction(string $csp): array
{
    if (! preg_match('/(?:^|;)\s*form-action\s+([^;]+)/', $csp, $m)) {
        return [];
    }

    return preg_split('/\s+/', trim($m[1])) ?: [];
}

function cspOrigin(string $url): string
{
    $parts = parse_url($url);

    return ($parts['scheme'] ?? 'https').'://'.($parts['host'] ?? '').(isset($parts['port']) ? ':'.$parts['port'] : '');
}

/** The origins this app's form-action must allow, for a given issuer. */
function cspExpectedOrigins(string $issuer): array
{
    if (NexoChrome::current() === 'nexoid') {
        $origins = [];
        foreach ((array) config('nexo-ecosystem.tools') as $key => $tool) {
            if ($key !== 'nexoid' && ! empty($tool['url'])) {
                $origins[] = cspOrigin((string) $tool['url']);
            }
        }

        return array_values(array_unique($origins));
    }

    return [cspOrigin($issuer)];
}

it('lets the forms reach Nexo ID (or, in Nexo ID, the tools)', function () {
    config()->set('nexo-sso.enabled', true);
    config()->set('nexo-sso.issuer', 'https://id.example.test');

    $sources = cspFormAction((string) test()->get('/')->headers->get('Content-Security-Policy'));

    expect($sources)->toContain("'self'");
    foreach (cspExpectedOrigins('https://id.example.test') as $origin) {
        expect(in_array($origin, $sources, true))->toBeTrue("form-action does not allow {$origin}: «Cerrar sesión» / the sign-in round trip is blocked after the POST.");
    }
});

it('stays same-origin when this tool runs standalone', function () {
    if (NexoChrome::current() === 'nexoid') {
        test()->markTestSkipped('Nexo ID always talks to its clients.');
    }

    config()->set('nexo-sso.enabled', false);

    $sources = cspFormAction((string) test()->get('/')->headers->get('Content-Security-Policy'));

    expect($sources)->toBe(["'self'"], 'Standalone, form-action must be exactly \'self\'.');
});

it('ships the same origins in the production .htaccess policy', function () {
    $htaccess = (string) @file_get_contents(public_path('.htaccess'));
    if (! preg_match('/Header always set Content-Security-Policy "([^"]*)"/', $htaccess, $m)) {
        test()->markTestSkipped('This app does not re-assert its CSP in public/.htaccess.');
    }

    // The production issuer is the registry's Nexo ID URL.
    $issuer = (string) config('nexo-ecosystem.tools.nexoid.url', 'https://nexoid.alvarocdev.com');
    $sources = cspFormAction($m[1]);

    expect($sources)->toContain("'self'");
    foreach (cspExpectedOrigins($issuer) as $origin) {
        expect(in_array($origin, $sources, true))->toBeTrue("public/.htaccess form-action does not allow {$origin} — LiteSpeed serves THIS policy in production.");
    }
});
