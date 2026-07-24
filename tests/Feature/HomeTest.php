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

it('marks upcoming tools and links live ones', function () {
    $this->get('/')
        ->assertSee(__('Próximamente'))
        ->assertSee(__('Usar'));
});

it('routes developers to the GitHub organization', function () {
    $this->get('/')->assertSee(config('nexo-ecosystem.github_org_url'), false);
});
