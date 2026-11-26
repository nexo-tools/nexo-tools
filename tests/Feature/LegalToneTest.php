<?php

// The legal pages speak the family register, tuteo neutro (STANDARD «Contenido
// base»), like every other screen. Nexo Tools' own Spanish text still said
// «decidís», «olvidás» and «aceptás» after nexo-ui v2.2 swept the voseo out of
// the template's legal base (usability pass, F3 2026-11-26). Same check as
// nexolinks' LegalToneTest.

test('the Spanish privacy and terms pages have no voseo', function (string $route): void {
    $html = (string) $this->get(route($route).'?lang=es')->assertOk()->getContent();
    $text = html_entity_decode(strip_tags($html));

    expect($text)->not->toMatch('/\b(sos|podés|tenés|querés|necesitás|aceptás|entrás|iniciás|creás|decidís|olvidás|usás|elegís|escribinos|contactanos)\b/iu');
})->with(['legal.privacy', 'legal.terms']);
