{{-- Standard ecosystem footer: "part of Nexo" -> hub, "powered by" attribution
     (canonical NEXO_ATTRIBUTION_LABEL/URL), a source link to the GitHub org and
     the legal pages. i18n: nexo.footer.* --}}
@php
    $eco = config('nexo-ecosystem', []);
    // Neutral product default: an instance somebody else deploys must not
    // advertise the upstream author (add-branding-footer, multi-instance rule).
    $attrLabel = config('nexo.attribution.label') ?: 'made with Nexo Tools';
    $attrUrl = config('nexo.attribution.url') ?: ($eco['github_org_url'] ?? 'https://github.com/nexo-tools');
@endphp

<footer {{ $attributes->merge(['class' => 'nexo-footer']) }}>
    <span class="nexo-footer__eco">
        <a href="{{ $eco['hub_url'] ?? 'https://nexotools.alvarocdev.com' }}" rel="noopener">
            {{ __('nexo.footer.part_of') }}
        </a>
    </span>

    <span class="nexo-footer__spacer"></span>

    {{-- The label is the whole phrase ("powered by example.com"): prepend
         nothing here, or the footer reads "Made by powered by example.com". --}}
    <span>
        <a href="{{ $attrUrl }}" rel="noopener">{{ $attrLabel }}</a>
    </span>

    <a href="{{ $eco['github_org_url'] ?? 'https://github.com/nexo-tools' }}" rel="noopener">
        {{ __('nexo.footer.source') }}
    </a>

    {{-- Help lives here for the same reason the legal pages do, and it took an
         audit to notice: it used to be a ghost button on the landing header
         only, so a signed-in owner inside the panel had no link to it. --}}
    <a href="{{ route('help') }}">{{ __('nexo.help.title') }}</a>

    {{-- Legal pages, reachable from every page: the footer is the only surface
         present on all of them, error pages included. Requires the legal.* routes
         (templates/nexo-ui/pages/legal/routes-snippet.php). --}}
    <a href="{{ route('legal.privacy') }}">{{ __('nexo.footer.privacy') }}</a>
    <a href="{{ route('legal.terms') }}">{{ __('nexo.footer.terms') }}</a>
</footer>
