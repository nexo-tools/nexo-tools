{{-- The hub's document scaffold. It exists so home.blade.php can be nothing but
     the five canonical sections: the body class that pins the footer to the
     bottom uses min-h-screen, and the family guardian (and STANDARD.md's
     anti-fingerprint grep) scans the landing view as a whole file. The layout
     owns the page, the view owns the content — the same split nexoshort uses. --}}
@props(['title', 'description'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['seo' => true])
        <x-nexo-seo
            :title="config('app.name').' — '.$title"
            :description="$description"
            :canonical="url('/')" />
    </head>
    <body class="flex min-h-screen flex-col bg-bg font-sans text-ink antialiased">
        <a href="#contenido" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded focus:bg-surface focus:px-4 focus:py-2 focus:text-link">
            {{ __('Skip to content') }}
        </a>

        {{-- Only the hero carries a primary CTA (design.md, "CTA voice"), so the
             header's action stays a ghost. --}}
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

        <main id="contenido" class="flex-1">
            {{ $slot }}
        </main>

        <x-nexo-footer />
    </body>
</html>
