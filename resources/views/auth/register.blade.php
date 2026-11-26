{{-- Local sign-up. It only exists in `local` auth mode (EnsureLocalAuth:register:
     404 in sso, Nexo ID's sign-up in transition), so what it creates is an
     account of THIS hub, nothing else. It used to promise «One account for all
     the Nexo tools» while doing exactly that (H-nexotools-04). With SSO on, the
     account that does open every tool is offered first: «Continuar con Nexo
     ID», the only violet button, and the local form after it with a ghost
     submit (the same order as the login, STANDARD «Login»). --}}
@php($ssoUrl = \App\Support\NexoChrome::ssoSignInUrl())
<x-guest-layout :title="__('Create account')">
    <h1 class="mb-1 text-xl font-semibold">{{ __('Create your account') }}</h1>
    <p class="mb-6 text-sm text-muted">{{ __('Your account for Nexo Tools.') }}</p>

    @if ($ssoUrl)
        <p class="mb-3 text-sm text-muted" data-nexo-id-explainer>{{ __('Nexo ID is your one account for every Nexo tool: sign in once and they all open.') }}</p>
        <a href="{{ $ssoUrl }}" class="nexo-btn nexo-btn--primary nexo-btn--block" data-nexo-sso-primary>{{ __('nexo.auth.continue') }}</a>
        <p class="nexo-divider">{{ __('Or create an account only for Nexo Tools') }}</p>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4"
          x-data="{ sending: false }" @submit="$nextTick(() => sending = true)">
        @csrf

        <x-field :label="__('Your name')" name="name" required autocomplete="name" />
        <x-field :label="__('Email')" name="email" type="email" required autocomplete="username" />
        <x-field :label="__('Password')" name="password" type="password" required autocomplete="new-password" />
        <x-field :label="__('Confirm password')" name="password_confirmation" type="password" required autocomplete="new-password" />

        @if ($ssoUrl)
            <button type="submit" class="nexo-btn nexo-btn--ghost w-full" :disabled="sending" :aria-busy="sending">{{ __('Create account') }}</button>
        @else
            <x-button ::disabled="sending" ::aria-busy="sending">{{ __('Create account') }}</x-button>
        @endif
    </form>

    <p class="mt-4 text-center text-sm text-muted">
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}" class="font-medium text-link hover:underline">{{ __('Sign in') }}</a>
    </p>
</x-guest-layout>
