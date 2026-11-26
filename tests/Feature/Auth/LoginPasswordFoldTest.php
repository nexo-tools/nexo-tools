<?php

// The hub's login view wires nexo-ui's `password-open` to the sso-client's
// NexoSsoNewAccount::awaitsPasswordSignIn() (v2.2, the expression the
// nexo-sso-client README documents): in transition, whoever chose «Ya tengo
// una cuenta con otro email» on «¿Primera vez?» lands on the login with the
// password fold already open — they came to type a password. The guardian
// (LoginNexoIdFirstTest) pins the component; this pins the wiring.

use App\Services\NexoSso\NexoSsoFlows;

require_once __DIR__.'/../NexoSso/NexoSsoTestSupport.php';

beforeEach(function (): void {
    config([
        'nexo-sso.enabled' => true,
        'nexo-sso.issuer' => 'https://nexoid.test',
        'nexo-sso.client_id' => '11111111-2222-3333-4444-555555555555',
        'nexo-sso.auth_mode' => 'transition',
        'nexo-sso.transition_until' => now()->addMonth()->format('Y-m-d'),
    ]);
});

/** The <details> of «Usar email y contraseña» on the rendered login. */
function hubPasswordFold(string $html): string
{
    expect(preg_match('/<details[^>]*data-nexo-password-fold[^>]*>/', $html, $match))->toBe(1, 'The transition login has no password fold.');

    return $match[0];
}

it('opens the password fold after «I already have an account with another email», and only then', function () {
    // Nobody waiting: the fold starts closed, Nexo ID first.
    expect(hubPasswordFold($this->get('/login')->assertOk()->getContent()))->not->toMatch('/\sopen\b/');

    // A Nexo ID that matches no account → «¿Primera vez?» → «Ya tengo una cuenta».
    nexoSsoFakeProvider(['access_token' => 'fake', 'token_type' => 'Bearer', 'id_token' => nexoSsoIdToken([
        'sub' => 'new-sub',
        'sid' => 'new-sid',
        'email' => 'nexo-person@example.com',
        'email_verified' => true,
        'name' => 'Nexo Person',
    ])]);
    nexoSsoCallback($this, mode: NexoSsoFlows::INTERACTIVE, return: '/app')->assertRedirect(route('nexo-sso.new-account'));
    $this->post(route('nexo-sso.new-account.store'), ['choice' => 'existing'])->assertRedirect(route('login'));

    expect(hubPasswordFold($this->get('/login')->assertOk()->getContent()))->toMatch('/\sopen\b/');

    // The 15 minutes are over: back to the closed fold.
    $this->travel(16)->minutes();
    expect(hubPasswordFold($this->get('/login')->assertOk()->getContent()))->not->toMatch('/\sopen\b/');
});
