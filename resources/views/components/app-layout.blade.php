{{-- Full-width authenticated shell (springboard, admin). The header is the
     canonical v2 one — the same on every layout, with the account menu (and the
     phone sheet) carrying «Cerrar sesión». Content goes in the default slot;
     `title` names the page in the tab («<Página> — Nexo Tools»). --}}
@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="flex min-h-screen flex-col bg-bg font-sans text-ink antialiased">
        <a href="#contenido" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded focus:bg-surface focus:px-4 focus:py-2 focus:text-link">
            {{ __('Skip to content') }}
        </a>

        <x-nexo-header />

        <main id="contenido" class="mx-auto w-full max-w-4xl flex-1 px-4 py-12">
            {{ $slot }}
        </main>

        <x-nexo-footer />
    </body>
</html>
