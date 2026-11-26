{{-- «Tus herramientas» (D7): the light launcher. Who is signed in and their
     Nexo ID account, then every tool of the registry, each opening its panel
     already signed in — the same links as the switcher
     (NexoChrome::switcherHref). Nothing to build by hand any more
     (H-nexotools-06). «Cerrar sesión» is the account menu's (and the phone
     sheet's), right above: a second sign-out form on the page is the drift
     AccountMenuTest forbids. --}}
<x-app-layout :title="__('nexo.app.panel')">
    {{-- How a «Link my account» ended, once; in transition, the offer itself
         (nexo-sso-client). Renders nothing otherwise. --}}
    <x-nexo-sso.transition-banner class="mb-6" />

    <header class="mb-8">
        <h1 class="text-2xl font-bold tracking-tight">{{ __('nexo.app.panel') }}</h1>
        <p class="mt-1 text-muted">{{ __('Open any Nexo tool: you go in with this same account.') }}</p>
    </header>

    <section class="mb-10 flex flex-wrap items-center gap-x-4 gap-y-4 rounded-2xl border border-line bg-surface-raised p-5 shadow-sm" data-launcher-identity
             aria-label="{{ __('nexo.account.menu') }}">
        <span class="nexo-avatar shrink-0" aria-hidden="true">{{ \App\Support\NexoChrome::initials($user) }}</span>
        <div class="min-w-0 flex-1 basis-48">
            <p class="truncate font-semibold" data-launcher-name>{{ $user->name }}</p>
            <p class="truncate text-sm text-muted" data-launcher-email>{{ $user->email }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            @if ($nexoIdAccountUrl)
                <a href="{{ $nexoIdAccountUrl }}" class="nexo-btn nexo-btn--ghost nexo-btn--sm" data-launcher-nexo-id>{{ __('nexo.account.nexo_id') }}</a>
            @endif
        </div>
    </section>

    <section aria-labelledby="launcher-tools">
        <h2 id="launcher-tools" class="sr-only">{{ __('The tools') }}</h2>
        <ul class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            @foreach ($tools as $tool)
                <li class="flex flex-col rounded-2xl border border-line bg-surface-raised p-5 shadow-sm" data-launcher-tool="{{ $tool['key'] }}"
                    @if ($tool['current']) aria-current="page" @endif>
                    <div class="flex items-start gap-3">
                        <img src="{{ $tool['mark'] }}" alt="" width="40" height="40" class="rounded-xl">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-lg font-semibold">{{ $tool['name'] }}</h3>
                                @if (! $tool['live'])
                                    <span class="nexo-badge-soon">{{ __('Coming soon') }}</span>
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-muted">{{ $tool['tagline'] }}</p>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-3">
                        @if ($tool['current'])
                            {{-- This very page: an «Abrir» here would only reload it. --}}
                            <span class="inline-flex min-h-9 items-center text-sm font-medium text-muted">{{ __('You are here') }}</span>
                        @elseif ($tool['href'])
                            <a href="{{ $tool['href'] }}" class="nexo-btn nexo-btn--primary nexo-btn--sm" data-launcher-open
                               aria-label="{{ __('Open :tool', ['tool' => $tool['name']]) }}">{{ __('Open') }}</a>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    </section>
</x-app-layout>
