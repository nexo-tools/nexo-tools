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

it('serves the verification notice to a signed-in unverified account', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)->get(route('verification.notice'))->assertOk();

    // Verified accounts have nothing to do here.
    $this->actingAs(User::factory()->create())
        ->get(route('verification.notice'))
        ->assertRedirect(route('dashboard'));
});
