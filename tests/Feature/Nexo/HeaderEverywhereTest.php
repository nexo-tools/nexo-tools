<?php

use App\Support\NexoChrome;
use Illuminate\Support\Facades\Route;

// Guardian (chrome v2, 2026-11-26): ONE header, the same on every layout of the
// app, and it knows who is signed in everywhere — landing, panel, help, legal,
// the 404 and «sesión cerrada» (CONTRATO-F3 §5.1, STANDARD «Header»).
//
// Why it exists: before v2 each app had a «panel» header and a «public» one.
// Off the panel nothing said you were signed in (help, legal and 404 showed no
// account at all), and the wordmark sent a signed-in person to the marketing
// page in four apps out of six (audit 2026-11-24).
//
// The 404 is the trap: Laravel renders an unmatched URL BEFORE the `web` group
// runs, so the error page has no session and shows a guest header to someone
// who is signed in. The cure is a last route `Route::fallback(fn () =>
// abort(404))` inside web.php (templates/nexo-ui/pages/README.md).
//
// Copy into tests/Feature/Nexo/. The pages come from route names every Nexo app
// shares; one that does not exist here is skipped, never failed.

/** Chrome pages as name => url, only the ones this app registers. */
function headerEverywherePages(bool $signedIn): array
{
    $pages = ['home' => url('/')];

    foreach (['help', 'legal.privacy', 'legal.terms', 'nexo-sso.signed-out'] as $name) {
        if (Route::has($name)) {
            $pages[$name] = route($name);
        }
    }

    if ($signedIn) {
        $pages['panel'] = NexoChrome::panelUrl();
    }

    $pages['404'] = url('/nexo-header-everywhere-'.substr(md5((string) microtime(true)), 0, 8));

    return $pages;
}

/** The rendered <header data-nexo-header> of a page. */
function headerEverywhereHeader(string $html, string $page): string
{
    $marker = strpos($html, 'data-nexo-header');
    expect($marker)->not->toBeFalse("[{$page}] does not render the canonical x-nexo-header.");

    $start = (int) strrpos(substr($html, 0, (int) $marker), '<header');
    $end = strpos($html, '</header>', (int) $marker);

    return substr($html, $start, ($end === false ? strlen($html) : $end) - $start);
}

/** What a header is made of: its chrome markers, in order (hrefs and aria-current vary per page). */
function headerEverywhereShape(string $header): string
{
    preg_match_all('/data-nexo-[a-z-]+/', $header, $m);

    return implode(' ', $m[0]);
}

function headerEverywhereSignIn(): void
{
    $model = config('auth.providers.users.model');
    test()->actingAs($model::factory()->create());
}

it('shows the account on every page of the app when signed in', function () {
    headerEverywhereSignIn();

    foreach (headerEverywherePages(true) as $page => $url) {
        $response = test()->followingRedirects()->get($url);
        expect(in_array($response->status(), [200, 404], true))->toBeTrue("[{$page}] answered {$response->status()}.");

        $header = headerEverywhereHeader($response->getContent(), $page);
        expect(str_contains($header, 'data-nexo-account-menu'))->toBeTrue("[{$page}] does not show the account menu to a signed-in person.");
        expect(str_contains($header, 'data-nexo-sign-in'))->toBeFalse("[{$page}] offers «Iniciar sesión» to a signed-in person.");
        expect(str_contains($header, 'data-nexo-mobile-menu'))->toBeTrue("[{$page}] has no phone sheet.");
    }
});

it('renders the 404 inside the web group, so it knows who is signed in', function () {
    // actingAs() puts the user straight on the guard, so a 404 rendered OUTSIDE
    // the web group would still look signed in here — while in a real browser
    // it has no session at all. What gives it away is the response itself: only
    // a request that went through StartSession sends the session cookie back.
    $response = test()->get(url('/nexo-header-everywhere-404-'.uniqid()))->assertNotFound();

    $cookies = array_map(fn ($cookie) => $cookie->getName(), $response->headers->getCookies());
    expect(in_array(config('session.cookie'), $cookies, true))->toBeTrue(
        'The 404 is rendered outside the web group (no session): add `Route::fallback(fn () => abort(404))` as the last route of routes/web.php.'
    );
});

it('renders the very same header on every layout', function () {
    headerEverywhereSignIn();

    $shapes = [];
    foreach (headerEverywherePages(true) as $page => $url) {
        $shapes[$page] = headerEverywhereShape(headerEverywhereHeader(test()->followingRedirects()->get($url)->getContent(), $page));
    }

    $reference = $shapes['panel'];
    foreach ($shapes as $page => $shape) {
        expect($shape)->toBe($reference, "The header of [{$page}] is not the panel's header — every layout must render <x-nexo-header> the same way.");
    }
});

it('sends the wordmark to the panel when signed in and to the landing when not', function () {
    $wordmark = function (string $html): ?string {
        return preg_match('/<a href="([^"]*)" class="nexo-header__brand"/', $html, $m) ? html_entity_decode($m[1]) : null;
    };

    foreach (headerEverywherePages(false) as $page => $url) {
        expect($wordmark(test()->get($url)->getContent()))->toBe(url('/'), "[{$page}]: without a session the wordmark goes to the landing.");
    }

    headerEverywhereSignIn();
    foreach (headerEverywherePages(true) as $page => $url) {
        expect($wordmark(test()->followingRedirects()->get($url)->getContent()))->toBe(NexoChrome::panelUrl(), "[{$page}]: with a session the wordmark goes to the panel.");
    }
});

it('offers «Iniciar sesión» on every page to a guest', function () {
    foreach (headerEverywherePages(false) as $page => $url) {
        $header = headerEverywhereHeader(test()->get($url)->getContent(), $page);

        expect(str_contains($header, 'data-nexo-sign-in'))->toBeTrue("[{$page}] has no «Iniciar sesión» in the header.");
        expect(str_contains($header, 'data-nexo-sheet-sign-in'))->toBeTrue("[{$page}] has no «Iniciar sesión» in the phone sheet.");
        expect(str_contains($header, 'data-nexo-account-menu'))->toBeFalse("[{$page}] shows an account menu to a guest.");
    }
});

it('keeps help, switcher, language and theme in the header', function () {
    $header = headerEverywhereHeader(test()->get(url('/'))->getContent(), 'home');

    foreach (['data-nexo-help-link', 'data-nexo-app-switcher', 'data-nexo-locale-switcher', 'data-nexo-theme-toggle'] as $marker) {
        expect(str_contains($header, $marker))->toBeTrue("The header lost [{$marker}].");
    }
});
