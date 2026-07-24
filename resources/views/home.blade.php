<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="flex min-h-screen flex-col bg-bg font-sans text-ink antialiased">
        <a href="#contenido" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded focus:bg-surface focus:px-4 focus:py-2 focus:text-brand-700">
            {{ __('Saltar al contenido') }}
        </a>

        <x-nexo-header brand="Nexo Tools" mark="/ecosystem/nexotools.svg">
            <x-slot:nav>
                <a href="{{ route('help') }}" class="nexo-btn nexo-btn--ghost">{{ __('nexo.help.title') }}</a>
            </x-slot:nav>
        </x-nexo-header>

        <main id="contenido" class="mx-auto w-full max-w-4xl flex-1 px-4 py-12">
            <header class="mb-10 text-center">
                <h1 class="text-3xl font-bold tracking-tight">{{ config('app.name') }}</h1>
                <p class="mt-2 text-muted">{{ __('Todas las herramientas Nexo, una sola cuenta.') }}</p>
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
                                        <span class="nexo-badge-soon">{{ __('Próximamente') }}</span>
                                    @endif
                                </div>
                                <p class="mt-1 text-sm text-muted">{{ $tool['tagline'] }}</p>
                            </div>
                        </div>
                        @if (($tool['status'] ?? 'live') === 'live' && $tool['url'])
                            <a href="{{ $tool['url'] }}" class="mt-4 inline-block rounded-lg bg-brand-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-brand-700">{{ __('Usar') }}</a>
                        @endif
                    </div>
                @endforeach
            </div>

            <section class="mt-12 rounded-2xl border border-line bg-surface-raised p-5 text-center">
                <p class="text-sm text-muted">
                    {{ __('¿Eres desarrollador?') }}
                    <a href="{{ $githubOrg }}" class="font-medium text-brand-700 hover:underline dark:text-brand-400" rel="noopener">{{ __('Explora el código en GitHub') }}</a>
                </p>
            </section>
        </main>

        <x-nexo-footer />
    </body>
</html>
