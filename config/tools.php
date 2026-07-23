<?php

// The Nexo ecosystem hub content. Tools and URLs are env-configurable so a
// self-hosted instance points at its own deployments; taglines are Spanish base
// strings (shown as-is). status: active | soon.
return [
    // Developer surface (the only technical link in the hub).
    'github_org' => env('NEXO_GITHUB_ORG', 'https://github.com/nexo-tools'),

    'tools' => [
        [
            'key' => 'nexolinks',
            'name' => 'Nexo Links',
            'tagline' => 'Compartí todos tus links en un solo lugar.',
            'url' => env('NEXO_URL_LINKS', 'https://nexolinks.alvarocdev.com'),
            'status' => 'active',
        ],
        [
            'key' => 'nexoagenda',
            'name' => 'Nexo Agenda',
            'tagline' => 'Recibí reservas para tu negocio en minutos.',
            'url' => env('NEXO_URL_AGENDA', 'https://nexoagenda.alvarocdev.com'),
            'status' => 'active',
        ],
        [
            'key' => 'nexoshort',
            'name' => 'Nexo Short',
            'tagline' => 'Acortá enlaces con métricas sin cookies.',
            'url' => env('NEXO_URL_SHORT', 'https://nxo.li'),
            'status' => 'soon',
        ],
        [
            'key' => 'nexoevents',
            'name' => 'Nexo Events',
            'tagline' => 'Creá eventos gratis y validá entradas con QR.',
            'url' => env('NEXO_URL_EVENTS', ''),
            'status' => 'soon',
        ],
    ],
];
