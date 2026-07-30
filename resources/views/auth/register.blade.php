<x-guest-layout>
    <h1 class="mb-1 text-xl font-bold">{{ __('Create your account') }}</h1>
    <p class="mb-6 text-sm text-muted">{{ __('One account for all the Nexo tools.') }}</p>

    <form method="POST" action="{{ route('register') }}" class="space-y-4"
          x-data="{ sending: false }" @submit="$nextTick(() => sending = true)">
        @csrf

        <x-field :label="__('Your name')" name="name" required autocomplete="name" />
        <x-field :label="__('Email')" name="email" type="email" required autocomplete="username" />
        <x-field :label="__('Password')" name="password" type="password" required autocomplete="new-password" />
        <x-field :label="__('Confirm password')" name="password_confirmation" type="password" required autocomplete="new-password" />

        <x-button ::disabled="sending" ::aria-busy="sending">{{ __('Create account') }}</x-button>
    </form>

    <p class="mt-4 text-center text-sm text-muted">
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}" class="font-medium text-link hover:underline">{{ __('Sign in') }}</a>
    </p>
</x-guest-layout>
