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
            :title="\App\Support\NexoChrome::homeTitle($title)"
            :description="$description"
            :canonical="url('/')" />
    </head>
    <body class="flex min-h-screen flex-col bg-bg font-sans text-ink antialiased">
        <a href="#contenido" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded focus:bg-surface focus:px-4 focus:py-2 focus:text-link">
            {{ __('Skip to content') }}
        </a>

        {{-- The same header as every other layout (chrome v2). Its sign-in is a
             ghost: only the hero carries a primary CTA (design.md, "CTA voice"). --}}
        <x-nexo-header />

        <main id="contenido" class="flex-1">
            {{ $slot }}
        </main>

        <x-nexo-footer />
    </body>
</html>
