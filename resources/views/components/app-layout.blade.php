{{-- Full-width authenticated shell (springboard, admin): shared Nexo chrome with
     a logout action in the header. Content goes in the default slot. --}}
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
            <x-slot:actions>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nexo-btn nexo-btn--ghost">{{ __('Cerrar sesión') }}</button>
                </form>
            </x-slot:actions>
        </x-nexo-header>

        <main id="contenido" class="mx-auto w-full max-w-4xl flex-1 px-4 py-12">
            {{ $slot }}
        </main>

        <x-nexo-footer />
    </body>
</html>
