<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="bg-brand-50 font-sans text-ink antialiased dark:bg-slate-900 dark:text-slate-200">
        <main id="contenido" class="mx-auto max-w-4xl px-4 py-12">
            <header class="mb-10 text-center">
                <span class="text-3xl font-bold tracking-tight">{{ config('app.name') }}</span>
                <p class="mt-2 text-slate-600 dark:text-slate-400">{{ __('Todas las herramientas Nexo, una sola cuenta.') }}</p>
            </header>

            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($tools as $tool)
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold">{{ $tool['name'] }}</h2>
                            <span @class([
                                'rounded-full px-2 py-0.5 text-xs font-medium',
                                'bg-green-100 text-green-800' => $tool['status'] === 'active',
                                'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300' => $tool['status'] !== 'active',
                            ])>{{ $tool['status'] === 'active' ? __('Activa') : __('Próximamente') }}</span>
                        </div>
                        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">{{ $tool['tagline'] }}</p>
                        @if ($tool['status'] === 'active' && $tool['url'])
                            <a href="{{ $tool['url'] }}" class="mt-3 inline-block rounded-lg bg-brand-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-brand-700">{{ __('Usar') }}</a>
                        @endif
                    </div>
                @endforeach
            </div>

            <section class="mt-12 rounded-2xl bg-slate-100 p-5 text-center dark:bg-slate-800/50">
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    {{ __('¿Eres desarrollador?') }}
                    <a href="{{ $githubOrg }}" class="font-medium text-brand-700 hover:underline dark:text-brand-400">{{ __('Explora el código en GitHub') }}</a>
                </p>
            </section>

            @if (config('nexo.attribution_text'))
                <footer class="mt-10 text-center text-xs text-slate-400">
                    @if (config('nexo.attribution_url'))
                        <a href="{{ config('nexo.attribution_url') }}" class="hover:underline">{{ config('nexo.attribution_text') }}</a>
                    @else
                        {{ config('nexo.attribution_text') }}
                    @endif
                </footer>
            @endif

            <x-locale-switcher class="mt-6 flex justify-center" />
        </main>
    </body>
</html>
