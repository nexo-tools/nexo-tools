<?php

namespace App\Http\Controllers;

use App\Models\BeaconEvent;
use App\Support\VisitorHash;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Cookieless ingestion for ecosystem pageviews. Always answers 204 (a beacon
 * never blocks the emitting page); whether it *writes* depends on the env gate,
 * Do Not Track and the origin allowlist. Never sets a cookie, never stores an IP
 * or User-Agent. (AC-BEACON-1..7)
 */
class BeaconController extends Controller
{
    public function __invoke(Request $request): Response
    {
        // Honour Do Not Track / Global Privacy Control regardless of anything
        // else: no write. (AC-BEACON-4)
        if ($request->header('DNT') === '1' || $request->header('Sec-GPC') === '1') {
            return $this->accepted();
        }

        // Opt-in per instance: off by default, so a standalone install ingests
        // nothing. (AC-BEACON-2)
        if (! config('nexo.beacon.enabled')) {
            return $this->accepted();
        }

        $payload = $this->payload($request);
        $origin = (string) ($payload['origin'] ?? '');

        // Only accept data from a known ecosystem origin — never arbitrary
        // senders. (AC-BEACON-3)
        $origins = (array) config('nexo.beacon.origins', []);
        if (! array_key_exists($origin, $origins)) {
            return $this->accepted();
        }

        $ref = (string) ($payload['ref'] ?? '');

        BeaconEvent::query()->create([
            'origin' => $origin,
            'path' => $this->cleanPath((string) ($payload['path'] ?? '/')),
            'visitor_hash' => VisitorHash::make($request), // anonymous, daily, never stored raw
            'day' => now()->toDateString(),
            'country' => $this->country($request),
            'ref' => array_key_exists($ref, $origins) ? $ref : null, // allowlisted tool slug only
        ]);

        return $this->accepted();
    }

    /**
     * Read the body from JSON, form or a text/plain sendBeacon blob.
     *
     * @return array<string, mixed>
     */
    private function payload(Request $request): array
    {
        $data = $request->all();

        if ($data === []) {
            $decoded = json_decode((string) $request->getContent(), true);
            if (is_array($decoded)) {
                $data = $decoded;
            }
        }

        return $data;
    }

    /** Strip query/fragment and cap at 255 — never the full URL, never PII. */
    private function cleanPath(string $path): string
    {
        $path = (string) parse_url($path, PHP_URL_PATH);
        if ($path === '') {
            $path = '/';
        }

        return mb_substr($path, 0, 255);
    }

    /** Coarse country from an edge header (Cloudflare) if present — no GeoIP lookup, no PII. */
    private function country(Request $request): ?string
    {
        $country = strtoupper((string) $request->header('CF-IPCountry', ''));

        return preg_match('/^[A-Z]{2}$/', $country) && $country !== 'XX' ? $country : null;
    }

    /** 204, no body, no cookie. */
    private function accepted(): Response
    {
        return response('', 204);
    }
}
