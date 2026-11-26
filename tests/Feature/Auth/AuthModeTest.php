<?php

// The hub's own doors per auth mode (NEXO_AUTH_MODE, nexo-sso-client v2.1).
// The hosted hub runs in sso (CONTRATO-F3 D2: 2 local accounts, 0 verified);
// a self-host keeps `local`. What lives in the template is pinned by
// tests/Feature/NexoSso; this file pins the hub's routes and screens.

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

function hubAuthMode(string $mode): void
{
    config()->set('nexo-sso.enabled', true);
    config()->set('nexo-sso.issuer', 'https://id.example.test');
    config()->set('nexo-sso.client_id', 'hub-client');
    config()->set('nexo-sso.auth_mode', $mode);
    config()->set('nexo-sso.transition_until', now()->addMonth()->format('Y-m-d'));
}

/** A discovery document, so the silent attempt can start (no network). */
function hubFakeProvider(): void
{
    Http::fake([
        'id.example.test/.well-known/openid-configuration' => Http::response([
            'issuer' => 'https://id.example.test',
            'authorization_endpoint' => 'https://id.example.test/oauth/authorize',
            'token_endpoint' => 'https://id.example.test/oauth/token',
            'jwks_uri' => 'https://id.example.test/oauth/jwks',
            'end_session_endpoint' => 'https://id.example.test/oauth/logout',
        ]),
    ]);
}

// ---- sso: only Nexo ID -----------------------------------------------------

it('closes every local credential door in sso mode', function () {
    hubAuthMode('sso');
    Notification::fake();
    $user = User::factory()->create();

    $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertNotFound();
    $this->get('/register')->assertNotFound();
    $this->post('/register', [
        'name' => 'Ana', 'email' => 'ana@example.test', 'password' => 'una-clave-larga-1', 'password_confirmation' => 'una-clave-larga-1',
    ])->assertNotFound();
    $this->get('/forgot-password')->assertNotFound();
    $this->post('/forgot-password', ['email' => $user->email])->assertNotFound();
    $this->get('/reset-password/some-token')->assertNotFound();
    $this->post('/reset-password', ['token' => 'x', 'email' => $user->email, 'password' => 'otra-clave-1', 'password_confirmation' => 'otra-clave-1'])->assertNotFound();

    $this->assertGuest();
    expect(User::query()->where('email', 'ana@example.test')->exists())->toBeFalse();
    Notification::assertNothingSent();
});

it('explains Nexo ID on the sso login, with «Continuar con Nexo ID» and «Crear cuenta» only', function () {
    hubAuthMode('sso');

    $html = $this->get('/login')->assertOk()->getContent();

    expect($html)
        ->toContain('data-nexo-id-explainer>'.__('Nexo ID is your one account for every Nexo tool: sign in once and they all open.'))
        ->toContain('data-nexo-sso-primary')
        ->toContain('href="https://id.example.test/register" data-nexo-sign-up>')
        ->not->toContain('name="password"')
        ->not->toContain('action="'.route('login').'"')
        ->not->toContain(route('password.request'));
});

it('sends a guest deep link through Nexo ID and back, never showing /login', function () {
    hubAuthMode('sso');

    $this->get('/app')->assertRedirect(route('nexo-sso.enter', ['to' => '/app']));
});

it('lets nobody in with an old local remember-me in sso mode', function () {
    // The 2 local, unverified accounts of production can't sign in any more,
    // and that is the point (CONTRATO-F3 D2): a session Nexo ID doesn't govern
    // is closed and sent through Nexo ID.
    hubAuthMode('sso');
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)->get('/app')->assertRedirect(route('nexo-sso.enter', ['to' => '/app']));
    $this->assertGuest();
});

// ---- transition --------------------------------------------------------------

it('sends the old «Create account» to Nexo ID\'s sign-up in transition, and refuses the local POST', function () {
    hubAuthMode('transition');

    $this->get('/register')->assertRedirect('https://id.example.test/register');
    $this->post('/register', ['name' => 'Ana', 'email' => 'ana@example.test'])->assertNotFound();
});

it('refuses the password of an account already linked to Nexo ID in transition', function () {
    hubAuthMode('transition');
    $user = User::factory()->create();
    $user->forceFill(['nexo_id_sub' => 'sub-linked'])->save();

    $this->from('/login')->post('/login', ['email' => $user->email, 'password' => 'password'])
        ->assertSessionHasErrors(['email' => __('This account already signs in with Nexo ID.')]);
    $this->assertGuest();
});

it('never honours the local remember-me outside local mode', function () {
    hubAuthMode('transition');
    $user = User::factory()->create();

    $this->post('/login', ['email' => $user->email, 'password' => 'password', 'remember' => 'on'])
        ->assertRedirect('/app')
        ->assertCookieMissing(Auth::guard('web')->getRecallerName());

    // Local mode does issue it: the assertion above is not vacuous.
    Auth::logout();
    hubAuthMode('local');
    $this->post('/login', ['email' => $user->email, 'password' => 'password', 'remember' => 'on'])
        ->assertCookie(Auth::guard('web')->getRecallerName());
});

it('sends no local reset link to an account linked to Nexo ID in transition, with the same answer', function () {
    hubAuthMode('transition');
    Notification::fake();
    $user = User::factory()->create();
    $user->forceFill(['nexo_id_sub' => 'sub-linked'])->save();

    $this->from('/forgot-password')->post('/forgot-password', ['email' => $user->email])
        ->assertRedirect('/forgot-password')
        ->assertSessionHas('status');

    Notification::assertNothingSent();
});

// ---- local (self-host default) -------------------------------------------------

it('keeps every local door open in local mode', function () {
    hubAuthMode('local');

    $this->get('/register')->assertOk();
    $this->get('/forgot-password')->assertOk();
    $this->get('/login')->assertOk()->assertSee('name="password"', false);
});

it('offers Nexo ID first on the local sign-up, and no longer promises one account for every tool', function () {
    // H-nexotools-04: the form creates an account of THIS hub only.
    hubAuthMode('local');

    $html = $this->get('/register')->assertOk()->getContent();

    expect($html)->not->toContain('One account for all the Nexo tools')
        ->and($html)->toContain(e(__('Your account for Nexo Tools.')))
        ->and(strpos($html, 'data-nexo-sso-primary'))->toBeLessThan(strpos($html, 'name="password"'));

    // Standalone, the local form is the only way in.
    config()->set('nexo-sso.enabled', false);
    expect($this->get('/register')->assertOk()->getContent())->not->toContain('data-nexo-sso-primary');
});

it('says where a Nexo ID password does work when the local one fails', function () {
    // H-nexotools-08: the likeliest mistake on a hub with Nexo ID on is a
    // Nexo ID password typed into the local form.
    hubAuthMode('local');
    $user = User::factory()->create();

    $this->from('/login')->post('/login', ['email' => $user->email, 'password' => 'mi-clave-de-nexo-id'])
        ->assertSessionHasErrors(['email' => __('That email and password don\'t match an account here. If you use Nexo ID, choose «Continue with Nexo ID».')]);

    config()->set('nexo-sso.enabled', false);
    $this->from('/login')->post('/login', ['email' => $user->email, 'password' => 'otra-mala'])
        ->assertSessionHasErrors(['email' => __('These credentials do not match our records.')]);
});

it('shows the error of a first failed sign-in instead of bouncing through Nexo ID', function () {
    // H-nexotools-03: POST /login → 302 /login used to fire the silent attempt
    // (the visitor already had the session cookie), and the CSP dropped that
    // hop: the button stayed disabled and the error was lost. The silent
    // attempt now never acts on the auth screens nor with errors in the session.
    hubAuthMode('local');
    config()->set('nexo-sso.silent', true);
    hubFakeProvider();
    $user = User::factory()->create();
    $cookie = (string) config('session.cookie');

    // Armed: a public page with the cookie and no errors does bounce.
    $this->withCookie($cookie, 'returning-visitor')->get('/help')
        ->assertRedirect(route('nexo-sso.silent', ['to' => '/help']));
    $this->flushSession(); // a fresh visit: the attempt is once per session

    $this->withCookie($cookie, 'returning-visitor')
        ->from('/login')
        ->followingRedirects()
        ->post('/login', ['email' => $user->email, 'password' => 'incorrecta'])
        ->assertOk()
        ->assertSee(e(__('That email and password don\'t match an account here. If you use Nexo ID, choose «Continue with Nexo ID».')), false)
        ->assertSee('action="'.route('login').'"', false);

    $this->assertGuest();
});
