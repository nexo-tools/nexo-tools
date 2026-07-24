<?php

// Guardian: the public "outside" of the hub (SEO/discovery). The home and help
// pages render a full <x-nexo-seo> head (description/canonical/OG/Twitter +
// theme-color + hreflang), and robots.txt / sitemap.xml serve the discovery
// surface (previously missing entirely).

it('serves meta description, canonical, open graph, theme-color and hreflang on the home page', function () {
    $html = $this->get('/')->assertOk()->getContent();

    expect($html)
        ->toContain('<meta name="description"')
        ->toContain('<link rel="canonical" href="'.url('/').'"')
        ->toContain('<meta property="og:title"')
        ->toContain('<meta property="og:url" content="'.url('/').'"')
        ->toContain('<meta property="og:image"')
        ->toContain('<meta name="theme-color"')
        ->toContain('hreflang="es"')
        ->toContain('hreflang="en"')
        ->toContain('hreflang="pt"')
        ->toContain('hreflang="x-default"')
        // The shared component's doc comment must stay a comment (no leaked literal
        // props) and prop values must be escaped exactly once (no double-encoding).
        ->not->toContain(':hreflang=')
        ->not->toContain(':noindex=')
        ->not->toContain('&amp;#0');

    // Exactly one <title> (the layout title is suppressed when the SEO component owns it).
    expect(substr_count($html, '<title>'))->toBe(1);
    expect(substr_count($html, 'name="theme-color"'))->toBe(1);
});

it('renders SEO on the help page too', function () {
    $html = $this->get('/help')->assertOk()->getContent();

    expect($html)
        ->toContain('<meta name="description"')
        ->toContain('<meta property="og:title"')
        ->toContain('hreflang="es"');
});

it('serves robots.txt with the private surface disallowed and a sitemap pointer', function () {
    $response = $this->get('/robots.txt');

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toContain('text/plain');
    expect($response->getContent())
        ->toContain('User-agent: *')
        ->toContain('Disallow: /app')
        ->toContain('Disallow: /admin')
        ->toContain('Disallow: /login')
        ->toContain('Disallow: /register')
        ->toContain('Disallow: /beacon')
        ->toContain('Sitemap: '.route('sitemap'));
});

it('serves a valid sitemap.xml listing the public pages', function () {
    $response = $this->get('/sitemap.xml');

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toContain('xml');
    expect($response->getContent())
        ->toContain('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">')
        ->toContain('<loc>'.url('/').'</loc>')
        ->toContain('<loc>'.route('help').'</loc>');

    expect(simplexml_load_string($response->getContent()))->not->toBeFalse();
});
