<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CORS for the beacon endpoint (AC-BEACON-6, v2.1 2026-11-26).
 *
 * The allowed origins are the ecosystem registry's (every tool's URL, plus
 * alvarocdev): config('nexo.beacon.origins'), derived from
 * config/nexo-ecosystem.php, so a local or self-hosted registry (NEXO_URL_*)
 * allows its own hosts. Anybody else gets no Access-Control-* header at all.
 *
 * Never with credentials: the beacon needs no cookie, and the emitter
 * (resources/js/nexo-beacon.js) sends a text/plain fetch with
 * credentials 'omit' — a simple request, no preflight. Before v2.1 it was a
 * credentialed sendBeacon with a JSON Blob, whose preflight this response could
 * not satisfy (it never says Allow-Credentials), and every page of every tool
 * logged a CORS error (H-nexoid-15).
 *
 * The preflight is still answered here, before the controller and the rate
 * limiter (an OPTIONS never writes and never spends the beacon budget): for an
 * allowed origin asking for POST, it allows POST and the Content-Type header.
 * The POST itself goes through the `beacon` throttle as before, and its 429 is
 * readable too (the throttle runs inside this middleware).
 */
class BeaconCors
{
    public function handle(Request $request, Closure $next): Response
    {
        $origin = (string) $request->headers->get('Origin', '');
        $allowed = $this->isAllowedOrigin($origin);

        if ($request->isMethod('OPTIONS')) {
            $response = response('', 204);
            $method = strtoupper((string) $request->headers->get('Access-Control-Request-Method', ''));

            if ($allowed && $method === 'POST') {
                $response->headers->set('Access-Control-Allow-Origin', $origin);
                $response->headers->set('Access-Control-Allow-Methods', 'POST');
                $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');
                $response->headers->set('Access-Control-Max-Age', '86400');
            }
        } else {
            $response = $next($request);

            if ($allowed) {
                $response->headers->set('Access-Control-Allow-Origin', $origin);
            }
        }

        // The answer depends on Origin, so a cache must key on it; and it is
        // never a credentialed CORS response.
        $response->setVary('Origin', false);
        $response->headers->remove('Access-Control-Allow-Credentials');

        return $response;
    }

    private function isAllowedOrigin(string $origin): bool
    {
        if ($origin === '') {
            return false;
        }

        $target = $this->normalize($origin);
        if ($target === null) {
            return false;
        }

        foreach ((array) config('nexo.beacon.origins', []) as $url) {
            if (is_string($url) && $url !== '' && $this->normalize($url) === $target) {
                return true;
            }
        }

        return false;
    }

    /** Reduce a URL to its origin (scheme://host[:port]), lowercased. */
    private function normalize(string $url): ?string
    {
        $parts = parse_url($url);
        if (! isset($parts['scheme'], $parts['host'])) {
            return null;
        }

        $normalized = strtolower($parts['scheme'].'://'.$parts['host']);

        return isset($parts['port']) ? $normalized.':'.$parts['port'] : $normalized;
    }
}
