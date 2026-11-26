<?php

// AC-BEACON-8: the emitter snippet is defined and wired into nexotools as the
// reference. It fires only when this instance opts in and respects Do Not Track.

it('AC-BEACON-8: renders the beacon emitter metas only when the beacon is enabled', function () {
    config(['nexo.beacon.enabled' => true]);

    $this->get('/')
        ->assertSee('name="nexo:beacon-endpoint"', false)
        ->assertSee('name="nexo:beacon-origin" content="nexotools"', false);
});

it('AC-BEACON-8: renders no emitter metas when the beacon is off (default/standalone)', function () {
    config(['nexo.beacon.enabled' => false]);

    $this->get('/')
        ->assertDontSee('nexo:beacon-endpoint', false)
        ->assertDontSee('nexo:beacon-origin', false);
});

it('AC-BEACON-8: the shareable snippet is the nexo-ui template one, wired into the app bundle', function () {
    // The send contract itself (fetch, credentials omit, keepalive, no
    // preflight) is pinned by the template guardian Nexo/BeaconClientTest; the
    // file is a byte-for-byte copy of templates/nexo-ui/beacon/nexo-beacon.js.
    expect(file_get_contents(resource_path('js/nexo-beacon.js')))
        ->toContain('doNotTrack')
        ->toContain("event: 'pageview'");
    expect(file_get_contents(resource_path('js/app.js')))->toContain('nexo-beacon.js');
});
