@php
    // Every row is something the code enforces, and the source is named next to
    // it. A row with no real source does not exist (design.md, "Copy"). The hub
    // is a directory, so its sheet describes the ecosystem and the cookieless
    // metric it runs — never features that would compete with the tools' own
    // landings (ADR-001).
    $specs = [
        // App\Support\VisitorHash — app key + date + IP + user agent, none stored.
        [__('Visitor metric'), __('Daily SHA-256 hash — nothing raw is stored, and days cannot be linked')],
        // App\Http\Controllers\BeaconController: DNT / Sec-GPC short-circuit before any write.
        [__('Do Not Track'), __('DNT=1 or Sec-GPC=1 → nothing is written')],
        // BeaconController::accepted() — response('', 204).
        [__('Beacon'), __('Always 204, no cookie and no session')],
        // config/nexo.php derives the allowlist from the registry plus author_url.
        [__('Accepted origins'), __('The registry\'s tools plus alvarocdev — a closed allowlist')],
        // config/nexo-sso.php — env-gated, so a standalone install works untouched.
        [__('One account'), __('Optional, through Nexo ID (OpenID Connect)')],
        // The generator's locales, plus the shared theme toggle.
        [__('Languages and themes'), __('es · en · pt — light and dark')],
    ];

    // The same answers the help center gives, in the same words — the landing
    // does not get a marketing rewrite of them.
    $faqs = array_slice((array) __('help.faqs'), 0, 4);
@endphp

<x-landing-layout :title="__('The open hub of the Nexo ecosystem.')"
                  :description="__('The Nexo ecosystem hub: open-source, self-hostable tools with a single account.')">

    <section data-landing-section="hero">
        <div class="mx-auto max-w-4xl px-6">
            <h1 class="max-w-2xl text-4xl font-semibold tracking-tight text-ink sm:text-5xl">{{ __('The open hub of the Nexo ecosystem.') }}</h1>
            <p class="mt-5 max-w-xl text-lg text-muted">{{ __('All the Nexo tools, one account.') }}</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a class="nexo-btn nexo-btn--primary" href="{{ route('register') }}">{{ __('Create your account') }}</a>
                <a class="nexo-btn nexo-btn--ghost" href="{{ route('login') }}">{{ __('Sign in') }}</a>
            </div>
        </div>
    </section>

    {{-- The documented variant (design.md, "Variants"): the hub is a directory,
         so its registry-driven grid IS the producto section. No figures here —
         the tools' own landings show their panels, and this page must not
         compete with them (ADR-001). The grid markup and its viewData are
         unchanged; HomeTest is the arbiter. --}}
    <section data-landing-section="producto">
        <div class="mx-auto max-w-4xl px-6">
            <h2 class="text-2xl font-semibold tracking-tight text-ink">{{ __('The tools') }}</h2>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                @foreach ($tools as $tool)
                    <div class="rounded-2xl border border-line bg-surface-raised p-5 shadow-sm">
                        <div class="flex items-start gap-3">
                            <img src="{{ $tool['mark'] }}" alt="" width="40" height="40" class="rounded-xl">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    {{-- h3, not h2: the section already owns the
                                         h2, and the page owns exactly one h1. --}}
                                    <h3 class="text-lg font-semibold">{{ $tool['name'] }}</h3>
                                    @if (($tool['status'] ?? 'live') !== 'live')
                                        <span class="nexo-badge-soon">{{ __('Coming soon') }}</span>
                                    @endif
                                </div>
                                <p class="mt-1 text-sm text-muted">{{ $tool['tagline'] }}</p>
                            </div>
                        </div>
                        @if (($tool['status'] ?? 'live') === 'live' && $tool['url'])
                            <a href="{{ $tool['url'] }}" class="nexo-btn nexo-btn--primary nexo-btn--sm mt-4">{{ __('Open') }}</a>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Kept from the landing this replaces, minus its centring: the
                 link is what HomeTest asks for, the centred box was never part
                 of the requirement and the family bans centring as an axis. --}}
            <p class="mt-8 text-sm text-muted">
                {{ __('Are you a developer?') }}
                <a href="{{ $githubOrg }}" class="inline-flex min-h-11 items-center font-medium text-link hover:underline" rel="noopener">{{ __('Explore the code on GitHub') }}</a>
            </p>
        </div>
    </section>

    <section data-landing-section="datos">
        <div class="mx-auto max-w-4xl px-6">
            <h2 class="text-2xl font-semibold tracking-tight text-ink">{{ __('The numbers') }}</h2>
            <dl class="mt-6 border-t border-line">
                @foreach ($specs as [$term, $value])
                    <div class="flex flex-col gap-1 border-b border-line py-3 sm:flex-row sm:gap-6 sm:py-4">
                        <dt class="text-sm font-medium text-ink sm:w-56 sm:shrink-0">{{ $term }}</dt>
                        <dd class="text-sm tabular-nums text-muted">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    <section data-landing-section="preguntas">
        <div class="mx-auto max-w-4xl px-6">
            <h2 class="text-2xl font-semibold tracking-tight text-ink">{{ __('Frequent questions') }}</h2>
            <div class="mt-6 border-t border-line">
                @foreach ($faqs as $faq)
                    <details class="border-b border-line">
                        <summary class="flex min-h-11 cursor-pointer items-center text-sm font-medium text-ink">{{ $faq['q'] ?? '' }}</summary>
                        <div class="pb-4 text-sm text-muted">{!! $faq['a'] ?? '' !!}</div>
                    </details>
                @endforeach
            </div>
            <p class="mt-4">
                {{-- inline-flex + min-h-11: a thumb needs 44px, and an inline
                     link is 18 (STANDARD.md, mobile-first). --}}
                <a href="{{ route('help') }}" class="inline-flex min-h-11 items-center text-sm font-medium text-link hover:underline">{{ __('All questions') }}</a>
            </p>
        </div>
    </section>

    <section data-landing-section="cierre">
        <div class="mx-auto max-w-4xl px-6">
            <a class="nexo-btn nexo-btn--primary" href="{{ route('register') }}">{{ __('Create your account') }}</a>
        </div>
    </section>
</x-landing-layout>
