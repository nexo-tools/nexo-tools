<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

{{-- Public pages own their whole SEO head via <x-nexo-seo> (title + theme-color
     included), so they include this partial with ['seo' => true] to suppress the
     plain title + theme-color below and avoid duplicating them. --}}
@unless ($seo ?? false)
    <title>{{ isset($title) ? $title.' — '.config('app.name') : config('app.name') }}</title>
@endunless

<link rel="icon" href="/favicon.ico" sizes="48x48">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
{{-- Same pair as <x-nexo-seo>: the browser chrome follows the page background
     per scheme. Literals because a <meta> content value can't read a CSS var. --}}
@unless ($seo ?? false)
    <meta name="theme-color" content="#f8fafc" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#020617" media="(prefers-color-scheme: dark)">
@endunless

@include('partials.theme-init')

@include('partials.beacon')

@vite(['resources/css/app.css', 'resources/js/app.js'])
