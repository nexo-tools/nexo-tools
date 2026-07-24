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

it('AC-BEACON-8: the shareable snippet respects Do Not Track and uses sendBeacon on pageload', function () {
    $source = file_get_contents(resource_path('js/nexo-beacon.js'));

    expect($source)
        ->toContain('doNotTrack')            // honours DNT
        ->toContain('globalPrivacyControl')  // honours GPC
        ->toContain('sendBeacon')            // non-blocking send
        ->toContain("event: 'pageview'");    // documented payload shape

    // And it is wired into the app bundle as the nexotools reference.
    expect(file_get_contents(resource_path('js/app.js')))->toContain('nexo-beacon.js');
});
