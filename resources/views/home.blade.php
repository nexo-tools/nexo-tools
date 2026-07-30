<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['seo' => true])
        <x-nexo-seo
            :title="config('app.name').' — '.__('All the Nexo tools, one account.')"
            :description="__('The Nexo ecosystem hub: open-source, self-hostable tools with a single account.')"
            :canonical="url('/')" />
    </head>
    <body class="flex min-h-screen flex-col bg-bg font-sans text-ink antialiased">
        <a href="#contenido" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded focus:bg-surface focus:px-4 focus:py-2 focus:text-link">
            {{ __('Skip to content') }}
        </a>

        <x-nexo-header brand="Nexo Tools" mark="/ecosystem/nexotools.svg">
            <x-slot:nav>
                <a href="{{ route('help') }}" class="nexo-btn nexo-btn--ghost">{{ __('nexo.help.title') }}</a>
            </x-slot:nav>
            <x-slot:actions>
                @auth
                    <a href="{{ route('dashboard') }}" class="nexo-btn nexo-btn--ghost">{{ __('Your tools') }}</a>
                @else
                    <a href="{{ route('login') }}" class="nexo-btn nexo-btn--ghost">{{ __('Sign in') }}</a>
                @endauth
            </x-slot:actions>
        </x-nexo-header>

        <main id="contenido" class="mx-auto w-full max-w-4xl flex-1 px-4 py-12">
            <header class="mb-10 text-center">
                <h1 class="text-3xl font-bold tracking-tight">{{ config('app.name') }}</h1>
                <p class="mt-2 text-muted">{{ __('All the Nexo tools, one account.') }}</p>
            </header>

            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($tools as $tool)
                    <div class="rounded-2xl border border-line bg-surface-raised p-5 shadow-sm">
                        <div class="flex items-start gap-3">
                            <img src="{{ $tool['mark'] }}" alt="" width="40" height="40" class="rounded-xl">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-lg font-semibold">{{ $tool['name'] }}</h2>
                                    @if (($tool['status'] ?? 'live') !== 'live')
                                        <span class="nexo-badge-soon">{{ __('Coming soon') }}</span>
                                    @endif
                                </div>
                                <p class="mt-1 text-sm text-muted">{{ $tool['tagline'] }}</p>
                            </div>
                        </div>
                        @if (($tool['status'] ?? 'live') === 'live' && $tool['url'])
                            <a href="{{ $tool['url'] }}" class="nexo-btn nexo-btn--primary nexo-btn--sm mt-4">{{ __('Open') }}</a>
                        @endif
                    </div>
                @endforeach
            </div>

            <section class="mt-12 rounded-2xl border border-line bg-surface-raised p-5 text-center">
                <p class="text-sm text-muted">
                    {{ __('Are you a developer?') }}
                    <a href="{{ $githubOrg }}" class="font-medium text-link hover:underline" rel="noopener">{{ __('Explore the code on GitHub') }}</a>
                </p>
            </section>
        </main>

        <x-nexo-footer />
    </body>
</html>
