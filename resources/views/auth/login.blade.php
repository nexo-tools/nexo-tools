{{-- Sign-in, chrome v2: «Continuar con Nexo ID» first and primary when SSO is
     on (x-nexo-sign-in-options, STANDARD «Login», LoginNexoIdFirstTest); the
     local form follows as the secondary way in, with a ghost submit so the
     screen has one violet button. The auth mode (NEXO_AUTH_MODE) decides
     whether the local form shows, folds or disappears: the hosted hub runs in
     sso, where only Nexo ID is left — so the screen says in one line what
     Nexo ID is (H-nexoshort-06, same here), next to «Crear cuenta». --}}
@php($ssoFirst = \App\Support\NexoChrome::ssoSignInUrl() !== null)
<x-guest-layout :title="__('Sign in')">
    <div class="mb-6">
        <h1 class="text-xl font-semibold">{{ __('Sign in to your account') }}</h1>
        @if ($ssoFirst)
            <p class="mt-2 text-sm text-muted" data-nexo-id-explainer>{{ __('Nexo ID is your one account for every Nexo tool: sign in once and they all open.') }}</p>
        @endif
    </div>

    {{-- transition: while «I already have an account with another email»
         waits, says what this password sign-in leads to (nexo-sso-client). --}}
    <x-nexo-sso.link-notice class="mb-4" />

    @if (session('status'))
        <p class="nexo-flash mb-4" role="status">{{ session('status') }}</p>
    @endif

    <x-nexo-sign-in-options>
        <form method="POST" action="{{ route('login') }}" class="space-y-4"
              x-data="{ sending: false }" @submit="$nextTick(() => sending = true)">
            @csrf

            <x-field :label="__('Email')" name="email" type="email" required autocomplete="username" />
            <x-field :label="__('Password')" name="password" type="password" required autocomplete="current-password" />

            @if (\App\Support\NexoChrome::allowsRemember())
                <label class="flex min-h-11 items-center gap-2 text-sm">
                    <input type="checkbox" name="remember" class="rounded border-control bg-surface text-primary focus:ring-ring">
                    {{ __('Remember me') }}
                </label>
            @endif

            @if ($ssoFirst)
                <button type="submit" class="nexo-btn nexo-btn--ghost w-full" :disabled="sending" :aria-busy="sending">{{ __('Sign in') }}</button>
            @else
                <x-button ::disabled="sending" ::aria-busy="sending">{{ __('Sign in') }}</x-button>
            @endif

            <p class="text-center text-sm">
                <a href="{{ route('password.request') }}" class="inline-flex min-h-11 items-center text-link hover:underline">
                    {{ __('Forgot your password?') }}
                </a>
            </p>
        </form>
    </x-nexo-sign-in-options>
</x-guest-layout>
