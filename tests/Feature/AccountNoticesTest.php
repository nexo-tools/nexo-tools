<?php

use App\Mail\NexoIdLinked;
use App\Mail\PasswordChanged;
use App\Models\User;
use App\Notifications\VerifyEmailQueued;
use App\Services\NexoSso\NexoSsoUserResolver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

/**
 * The hub had one mail — the framework's English password reset — and no
 * verification at all. These are the four the family standard requires.
 */
it('sends the verification link when an account is created', function () {
    Notification::fake();

    $user = User::factory()->unverified()->create();
    event(new Registered($user));

    // Anybody could register somebody else's address and the owner would never
    // have heard about it.
    Notification::assertSentTo($user, VerifyEmailQueued::class);
});

it('tells the owner when their password is reset', function () {
    Mail::fake();

    $user = User::factory()->create(['email' => 'ana@example.com']);
    $token = Password::createToken($user);

    $this->post(route('password.store'), [
        'token' => $token,
        'email' => $user->email,
        'password' => 'a-brand-new-password',
        'password_confirmation' => 'a-brand-new-password',
    ])->assertRedirect();

    Mail::assertQueued(PasswordChanged::class, fn (PasswordChanged $mail): bool => $mail->hasTo('ana@example.com'));
});

it('tells the owner the first time Nexo ID is linked, and only then', function () {
    Mail::fake();

    User::factory()->create(['email' => 'ana@example.com']);
    $claims = ['sub' => 'sub-1', 'email' => 'ana@example.com', 'email_verified' => true, 'name' => 'Ana'];

    app(NexoSsoUserResolver::class)->resolve($claims);
    app(NexoSsoUserResolver::class)->resolve($claims);

    Mail::assertQueued(NexoIdLinked::class, fn (NexoIdLinked $mail): bool => $mail->hasTo('ana@example.com'));
    Mail::assertQueuedCount(1);
});

/*
 * The "Nexo ID was linked" mail follows the reference text of the
 * nexo-sso-client README (v2.2): no «identity provider» jargon, «unir» in
 * Spanish, and the password lines only in local mode (in transition and sso a
 * linked account signs in with Nexo ID alone).
 */
it('says the password keeps working only in local mode', function (string $mode, string $how, string $body, string $footer) {
    config(['nexo-sso.enabled' => true, 'nexo-sso.auth_mode' => $mode, 'nexo-sso.transition_until' => now()->addMonth()->format('Y-m-d')]);
    $user = User::factory()->create(['email' => 'ana@example.com']);

    $html = html_entity_decode((new NexoIdLinked($user, $how))->locale('en')->render());

    expect($html)->toContain($body)->toContain($footer);
    if ($mode !== 'local') {
        expect($html)->not->toContain('Your password keeps working')
            ->not->toContain('change your password');
    }
})->with([
    'local, by email' => ['local', NexoSsoUserResolver::BY_EMAIL,
        'From now on you can sign in here with your Nexo ID. Your password keeps working — nothing else changed.',
        'If you did not do this, change your password and write to us: Nexo ID confirmed that this email address is yours, so someone with access to it linked the account.'],
    'local, explicit' => ['local', NexoSsoUserResolver::EXPLICIT,
        'From now on you can sign in here with your Nexo ID. Your password keeps working — nothing else changed.',
        'If you did not do this, change your password and write to us: someone who knew your password signed in here and linked the account.'],
    'transition, explicit' => ['transition', NexoSsoUserResolver::EXPLICIT,
        'From now on you sign in here with your Nexo ID — nothing else changed.',
        'If you did not do this, write to us: someone who knew your password signed in here and linked the account.'],
    'sso, by email' => ['sso', NexoSsoUserResolver::BY_EMAIL,
        'From now on you sign in here with your Nexo ID — nothing else changed.',
        'If you did not do this, write to us: Nexo ID confirmed that this email address is yours, so someone with access to it linked the account.'],
]);

it('names Nexo ID in every language, never an «identity provider»', function (string $mode, string $locale, string $subject, string $expected) {
    config(['nexo-sso.enabled' => true, 'nexo-sso.auth_mode' => $mode, 'nexo-sso.transition_until' => now()->addMonth()->format('Y-m-d')]);
    $user = User::factory()->create(['email' => 'ana@example.com']);

    $mail = (new NexoIdLinked($user, NexoSsoUserResolver::BY_EMAIL))->locale($locale);
    $html = html_entity_decode($mail->render());

    $mail->assertHasSubject($subject);
    expect($html)->toContain($subject)
        ->toContain($expected)
        ->not->toMatch('/identity provider|proveedor de identidad|provedor de identidade/i');
})->with([
    'sso, es' => ['sso', 'es', 'Tu cuenta quedó unida a Nexo ID', 'Si no fuiste tú, escríbenos: Nexo ID confirmó que ese correo es tuyo, así que alguien con acceso a él unió la cuenta.'],
    'local, es' => ['local', 'es', 'Tu cuenta quedó unida a Nexo ID', 'Si no fuiste tú, cambia tu contraseña y escríbenos: Nexo ID confirmó que ese correo es tuyo'],
    'transition, en' => ['transition', 'en', 'Nexo ID was linked to your account', 'Nexo ID confirmed that this email address is yours'],
    'sso, pt' => ['sso', 'pt', 'O Nexo ID foi vinculado à sua conta', 'o Nexo ID confirmou que esse e-mail é seu'],
]);

it('says «unir» in Spanish, as every screen does («Unir mi cuenta»), never «vincular»', function (string $mode, string $how) {
    config(['nexo-sso.enabled' => true, 'nexo-sso.auth_mode' => $mode, 'nexo-sso.transition_until' => now()->addMonth()->format('Y-m-d')]);
    $user = User::factory()->create(['email' => 'ana@example.com']);

    $html = html_entity_decode((new NexoIdLinked($user, $how))->locale('es')->render());

    expect($html)->toContain('unid')->not->toMatch('/vincul/iu');
})->with([
    'local, by email' => ['local', NexoSsoUserResolver::BY_EMAIL],
    'local, explicit' => ['local', NexoSsoUserResolver::EXPLICIT],
    'transition, explicit' => ['transition', NexoSsoUserResolver::EXPLICIT],
    'sso, by email' => ['sso', NexoSsoUserResolver::BY_EMAIL],
]);

it('serves the verification notice to a signed-in unverified account', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)->get(route('verification.notice'))->assertOk();

    // Verified accounts have nothing to do here.
    $this->actingAs(User::factory()->create())
        ->get(route('verification.notice'))
        ->assertRedirect(route('dashboard'));
});
