<?php

it('shows the ecosystem hub with every tool', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Nexo ID')
        ->assertSee('Nexo Links')
        ->assertSee('Nexo Agenda')
        ->assertSee('Nexo Short')
        ->assertSee('Nexo Events');
});

it('does not list the hub itself in its own grid', function () {
    // nexotools is the current tool; the grid shows the *other* ecosystem tools.
    $tools = $this->get('/')->viewData('tools');

    expect($tools)->not->toHaveKey('nexotools');
});

it('links live tools and badges upcoming ones, straight from the registry', function () {
    // Dynamic guardian: a new tool enters the registry as 'soon' (badge shown)
    // and flips to 'live' at launch — this test follows the registry instead
    // of hardcoding either state.
    $response = $this->get('/')->assertSee(__('Abrir'));

    collect(config('nexo-ecosystem.tools'))
        ->except('nexotools')
        ->contains(fn (array $tool): bool => ($tool['status'] ?? 'live') === 'soon')
            ? $response->assertSee(__('Próximamente'))
            : $response->assertDontSee(__('Próximamente'));
});

it('routes developers to the GitHub organization', function () {
    $this->get('/')->assertSee(config('nexo-ecosystem.github_org_url'), false);
});
