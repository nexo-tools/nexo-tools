<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CORS for the beacon endpoint. Emits Access-Control-Allow-Origin ONLY for hosts
 * in the ecosystem allowlist (the tools + alvarocdev), so those subdomains may
 * post but nobody else can. Answers the preflight OPTIONS directly. (AC-BEACON-6)
 */
class BeaconCors
{
    public function handle(Request $request, Closure $next): Response
    {
        $origin = (string) $request->headers->get('Origin', '');
        $allowed = $this->isAllowedOrigin($origin);

        // Preflight: reply here without touching the controller (or rate limiter).
        $response = $request->getMethod() === 'OPTIONS'
            ? response('', 204)
            : $next($request);

        if ($allowed) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Vary', 'Origin');
            $response->headers->set('Access-Control-Allow-Methods', 'POST, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');
            $response->headers->set('Access-Control-Max-Age', '86400');
        }

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
