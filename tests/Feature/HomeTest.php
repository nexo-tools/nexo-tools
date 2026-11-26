<?php

use App\Models\User;

it('shows the ecosystem hub with every tool', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Nexo ID')
        ->assertSee('Nexo Links')
        ->assertSee('Nexo Agenda')
        ->assertSee('Nexo Short')
        ->assertSee('Nexo Events');
});

it('does not list the hub itself in its own grid', function () {
    // nexotools is the current tool; the grid shows the *other* ecosystem tools.
    $tools = $this->get('/')->viewData('tools');

    expect($tools)->not->toHaveKey('nexotools');
});

it('links live tools and badges upcoming ones, straight from the registry', function () {
    // Dynamic guardian: a new tool enters the registry as 'soon' (badge shown)
    // and flips to 'live' at launch — this test follows the registry instead
    // of hardcoding either state.
    $response = $this->get('/')->assertSee(__('Open'));

    collect(config('nexo-ecosystem.tools'))
        ->except('nexotools')
        ->contains(fn (array $tool): bool => ($tool['status'] ?? 'live') === 'soon')
            ? $response->assertSee(__('Coming soon'))
            : $response->assertDontSee(__('Coming soon'));
});

it('routes developers to the GitHub organization', function () {
    $this->get('/')->assertSee(config('nexo-ecosystem.github_org_url'), false);
});

it('sends the hero\'s sign-up and sign-in where the auth mode says', function (string $mode, string $signUp, string $signIn) {
    // Chrome v2: the landing must not offer the local /register or /login in
    // the modes that close them — the header already goes to Nexo ID there.
    config()->set('nexo-sso.enabled', true);
    config()->set('nexo-sso.issuer', 'https://id.example.test');
    config()->set('nexo-sso.auth_mode', $mode);
    config()->set('nexo-sso.transition_until', now()->addMonth()->format('Y-m-d'));

    $html = $this->get('/')->assertOk()->getContent();

    expect(substr_count($html, 'href="'.e($signUp).'">'.__('Create your account')))->toBe(2)
        ->and(str_contains($html, 'href="'.$signIn.'">'.__('Sign in')))->toBeTrue();
})->with([
    'local' => fn () => ['local', route('register'), route('login')],
    // nexo-ui v2.2: outside local, the sign-up goes through the entry route and
    // comes back to /app signed in (it no longer strands the person in Nexo ID).
    'transition' => fn () => ['transition', route('nexo-sso.enter', ['to' => '/app', 'signup' => 1]), route('login')],
    'sso' => fn () => ['sso', route('nexo-sso.enter', ['to' => '/app', 'signup' => 1]), route('nexo-sso.enter')],
]);

it('leads a signed-in person into «Tus herramientas» instead of offering to sign in', function () {
    // H-nexotools-10: the hero and the closing block used to offer «Create your
    // account» and «Sign in» to someone who was already in.
    config()->set('nexo-sso.enabled', true);
    config()->set('nexo-sso.issuer', 'https://id.example.test');
    config()->set('nexo-sso.auth_mode', 'local');

    $html = $this->actingAs(User::factory()->create())
        ->get('/')->assertOk()->getContent();

    $hero = substr($html, strpos($html, 'data-hero-actions'));
    $hero = substr($hero, 0, strpos($hero, '</div>'));

    expect(substr_count($html, 'href="'.route('dashboard').'">'.e(__('Go to your tools'))))->toBe(2)
        ->and($hero)->not->toContain(e(__('Create your account')))
        ->and($hero)->not->toContain('https://id.example.test/register');
});

it('talks to a visitor in plain words, not in engineering terms', function () {
    // H-nexotools-09: «Hash SHA-256 diario», «DNT=1 o Sec-GPC=1», «Beacon
    // siempre 204», «allowlist cerrada», «OpenID Connect»… The technical
    // detail belongs in the help center and on GitHub.
    foreach (['es', 'en', 'pt'] as $locale) {
        $html = $this->get('/?lang='.$locale)->assertOk()->getContent();
        $text = strip_tags(substr($html, strpos($html, '<main')));

        foreach (['SHA-256', 'DNT=1', 'Sec-GPC', 'Beacon', 'allowlist', 'OpenID', 'self-host', 'autohosped', 'standalone', '204'] as $jargon) {
            expect(str_contains($text, $jargon))->toBeFalse("«{$jargon}» on the {$locale} landing");
        }
    }
});
