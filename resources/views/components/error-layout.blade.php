@props(['code', 'title', 'message'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="flex min-h-screen flex-col bg-bg font-sans text-ink antialiased">
        <x-nexo-header brand="Nexo Tools" mark="/ecosystem/nexotools.svg" />

        <main class="flex flex-1 flex-col items-center justify-center px-4 py-10 text-center">
            <p class="text-6xl font-bold tabular-nums text-brand-700 dark:text-brand-400">{{ $code }}</p>
            <h1 class="mt-4 text-2xl font-semibold">{{ $title }}</h1>
            <p class="mt-2 max-w-sm text-muted">{{ $message }}</p>

            <a href="{{ url('/') }}"
               class="mt-8 inline-flex items-center justify-center rounded-lg bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2">
                {{ __('Volver al inicio') }}
            </a>
        </main>

        <x-nexo-footer />
    </body>
</html>
