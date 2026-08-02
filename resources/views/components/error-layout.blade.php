@props(['code', 'title', 'message'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        {{-- An error page has no business in a search result. Declared here
             rather than passed down because partials.head builds its own head
             and takes no noindex flag. --}}
        <meta name="robots" content="noindex">
        @include('partials.head')
    </head>
    <body class="flex min-h-screen flex-col bg-bg font-sans text-ink antialiased">
        <a href="#contenido" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded focus:bg-surface focus:px-4 focus:py-2 focus:text-link">
            {{ __('Skip to content') }}
        </a>

        <x-nexo-header brand="Nexo Tools" mark="/ecosystem/nexotools.svg" />

        <main id="contenido" class="flex flex-1 flex-col items-center justify-center px-4 py-10 text-center">
            <p class="text-6xl font-bold tabular-nums text-primary">{{ $code }}</p>
            <h1 class="mt-4 text-2xl font-semibold">{{ $title }}</h1>
            <p class="mt-2 max-w-sm text-muted">{{ $message }}</p>

            <a href="{{ url('/') }}" class="nexo-btn nexo-btn--primary mt-8">
                {{ __('Back to home') }}
            </a>
        </main>

        <x-nexo-footer />
    </body>
</html>
