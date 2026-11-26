<?php

use Illuminate\Support\Facades\Route;

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

    expect(substr_count($html, 'href="'.$signUp.'">'.__('Create your account')))->toBe(2)
        ->and(str_contains($html, 'href="'.$signIn.'">'.__('Sign in')))->toBeTrue();
})->with([
    'local' => fn () => ['local', route('register'), route('login')],
    'transition' => fn () => ['transition', 'https://id.example.test/register', route('login')],
    // In sso, the entry route of session v2 once it is in, the authorize until then.
    'sso' => fn () => ['sso', 'https://id.example.test/register', route(Route::has('nexo-sso.enter') ? 'nexo-sso.enter' : 'nexo-sso.redirect')],
]);
