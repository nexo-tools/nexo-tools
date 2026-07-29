{{-- Standard ecosystem footer: "part of Nexo" -> hub, "powered by" attribution
     (canonical NEXO_ATTRIBUTION_LABEL/URL), a source link to the GitHub org and
     the legal pages. i18n: nexo.footer.* --}}
@php
    $eco = config('nexo-ecosystem', []);
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

    <span>
        <a href="{{ $attrUrl }}" rel="noopener">{{ $attrLabel }}</a>
    </span>

    <a href="{{ $eco['github_org_url'] ?? 'https://github.com/nexo-tools' }}" rel="noopener">
        {{ __('nexo.footer.source') }}
    </a>

    {{-- Legal pages, reachable from every page: the footer is the only surface
         present on all of them, error pages included. Requires the legal.* routes
         (templates/nexo-ui/pages/legal/routes-snippet.php). --}}
    <a href="{{ route('legal.privacy') }}">{{ __('Privacidad') }}</a>
    <a href="{{ route('legal.terms') }}">{{ __('Términos') }}</a>
</footer>
