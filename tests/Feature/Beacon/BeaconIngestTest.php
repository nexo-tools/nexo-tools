<?php

use App\Models\BeaconEvent;
use Illuminate\Support\Facades\Schema;

// The ingestion contract for the cookieless ecosystem beacon. A known-good
// origin from the registry allowlist (nexolinks) is used throughout.
const ALLOWED_ORIGIN = 'nexolinks';
const ALLOWED_HOST = 'https://nexolinks.alvarocdev.com';

beforeEach(function () {
    config(['nexo.beacon.enabled' => true]);
});

it('AC-BEACON-1: stores a pageview with an anonymous hash and a truncated path, then 204', function () {
    $longPath = '/'.str_repeat('a', 400);

    $response = $this->postJson('/beacon', [
        'origin' => ALLOWED_ORIGIN,
        'path' => $longPath,
        'event' => 'pageview',
    ]);

    $response->assertNoContent(); // 204

    $event = BeaconEvent::query()->sole();
    expect($event->origin)->toBe(ALLOWED_ORIGIN)
        ->and(strlen($event->path))->toBe(255)          // truncated to 255
        ->and($event->visitor_hash)->toMatch('/^[0-9a-f]{64}$/') // SHA-256, anonymous
        ->and($event->day->toDateString())->toBe(now()->toDateString());
});

it('AC-BEACON-1: derives the visitor_hash and never stores the raw IP or User-Agent', function () {
    $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.9'])
        ->postJson('/beacon', ['origin' => ALLOWED_ORIGIN, 'path' => '/x'], ['User-Agent' => 'CanaryUA/1.0'])
        ->assertNoContent();

    $event = BeaconEvent::query()->sole();
    expect($event->visitor_hash)
        ->not->toContain('203.0.113.9')
        ->not->toContain('CanaryUA')
        ->toHaveLength(64);
});

it('AC-BEACON-2: with the beacon disabled (default), answers 204 but writes nothing', function () {
    config(['nexo.beacon.enabled' => false]);

    $this->postJson('/beacon', ['origin' => ALLOWED_ORIGIN, 'path' => '/x'])
        ->assertNoContent();

    expect(BeaconEvent::query()->count())->toBe(0);
});

it('AC-BEACON-3: an origin outside the allowlist answers 204 but writes nothing', function () {
    $this->postJson('/beacon', ['origin' => 'evil-corp', 'path' => '/x'])
        ->assertNoContent();

    expect(BeaconEvent::query()->count())->toBe(0);
});

it('AC-BEACON-4: honours Do Not Track — 204 and writes nothing', function () {
    $this->postJson('/beacon', ['origin' => ALLOWED_ORIGIN, 'path' => '/x'], ['DNT' => '1'])
        ->assertNoContent();

    expect(BeaconEvent::query()->count())->toBe(0);
});

it('AC-BEACON-4: honours Global Privacy Control (Sec-GPC) — 204 and writes nothing', function () {
    $this->postJson('/beacon', ['origin' => ALLOWED_ORIGIN, 'path' => '/x'], ['Sec-GPC' => '1'])
        ->assertNoContent();

    expect(BeaconEvent::query()->count())->toBe(0);
});

it('AC-BEACON-5: rate-limits by IP and returns 429 past the cap', function () {
    config(['nexo.beacon.rate_limit' => 3]);

    for ($i = 0; $i < 3; $i++) {
        $this->postJson('/beacon', ['origin' => ALLOWED_ORIGIN, 'path' => '/x'])->assertNoContent();
    }

    $this->postJson('/beacon', ['origin' => ALLOWED_ORIGIN, 'path' => '/x'])->assertStatus(429);
});

it('AC-BEACON-6: sends Access-Control-Allow-Origin only to allowlisted origins', function () {
    $allowed = $this->withHeader('Origin', ALLOWED_HOST)
        ->postJson('/beacon', ['origin' => ALLOWED_ORIGIN, 'path' => '/x']);
    $allowed->assertHeader('Access-Control-Allow-Origin', ALLOWED_HOST);

    $rejected = $this->withHeader('Origin', 'https://attacker.example')
        ->postJson('/beacon', ['origin' => ALLOWED_ORIGIN, 'path' => '/x']);
    expect($rejected->headers->has('Access-Control-Allow-Origin'))->toBeFalse();
});

it('AC-BEACON-6: answers the preflight OPTIONS for an allowlisted origin', function () {
    $response = $this->call('OPTIONS', '/beacon', [], [], [], [
        'HTTP_ORIGIN' => ALLOWED_HOST,
        'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'POST',
    ]);

    expect($response->getStatusCode())->toBe(204);
    $response->assertHeader('Access-Control-Allow-Origin', ALLOWED_HOST);
    expect($response->headers->get('Access-Control-Allow-Methods'))->toContain('POST');
    // Preflight must not write.
    expect(BeaconEvent::query()->count())->toBe(0);
});

it('AC-BEACON-7: never sets a cookie and the table has no ip/ua columns', function () {
    $response = $this->postJson('/beacon', ['origin' => ALLOWED_ORIGIN, 'path' => '/x']);

    // No Set-Cookie on the beacon response (cookieless).
    expect($response->headers->getCookies())->toBe([])
        ->and($response->headers->has('Set-Cookie'))->toBeFalse();

    // The schema itself cannot hold an IP or User-Agent.
    $columns = Schema::getColumnListing('beacon_events');
    expect($columns)->not->toContain('ip')
        ->not->toContain('ip_address')
        ->not->toContain('user_agent')
        ->not->toContain('ua');
});
