<?php

// The ecosystem registry is required directly (not via config()) so the beacon
// origin allowlist derives deterministically from a single source, regardless of
// config load order and safe under `config:cache`.
$ecosystem = require __DIR__.'/nexo-ecosystem.php';

// Accepted beacon origins: slug => canonical URL. Keys validate the body's
// `origin`/`ref`; hosts gate CORS. Derived from every ecosystem tool plus
// alvarocdev (the author site — not a tool in the registry, but an emitter).
$beaconOrigins = [];
foreach ($ecosystem['tools'] ?? [] as $slug => $tool) {
    $beaconOrigins[$slug] = $tool['url'] ?? null;
}
$beaconOrigins['alvarocdev'] = $ecosystem['author_url'] ?? null;

return [

    // Powered-by attribution shown in the shared footer. Canonical ecosystem
    // contract (same name/shape across every Nexo tool): NEXO_ATTRIBUTION_*.
    'attribution' => [
        'label' => env('NEXO_ATTRIBUTION_LABEL'),
        'url' => env('NEXO_ATTRIBUTION_URL'),
    ],

    // Help center contact target (mailto is used when no support URL is set).
    'support_url' => env('NEXO_SUPPORT_URL'),
    'support_email' => env('NEXO_SUPPORT_EMAIL', 'hola@alvarocdev.com'),

    // Cookieless ecosystem analytics (opt-in). Off by default so the app runs
    // standalone: /beacon answers 204 but writes nothing. See AGENTS.md.
    'beacon' => [
        'enabled' => (bool) env('NEXO_BEACON_ENABLED', false),

        // Where the browser snippet posts. The hub is same-origin (/beacon);
        // other tools point at the hub's absolute URL.
        'endpoint' => (string) env('NEXO_BEACON_ENDPOINT', '/beacon'),

        // Requests per minute per IP before 429. (AC-BEACON-5)
        'rate_limit' => (int) env('NEXO_BEACON_RATE_LIMIT', 60),

        // slug => canonical URL. array_keys() are the valid body origins;
        // the hosts are the CORS allowlist. (AC-BEACON-3, AC-BEACON-6)
        'origins' => $beaconOrigins,
    ],

];
