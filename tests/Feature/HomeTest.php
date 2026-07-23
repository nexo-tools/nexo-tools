<?php

it('shows the ecosystem hub with every tool', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Nexo Links')
        ->assertSee('Nexo Agenda')
        ->assertSee('Nexo Short')
        ->assertSee('Nexo Events');
});

it('marks active vs upcoming tools', function () {
    $this->get('/')
        ->assertSee(__('Activa'))
        ->assertSee(__('Próximamente'));
});

it('routes developers to the GitHub organization', function () {
    $this->get('/')->assertSee(config('tools.github_org'), false);
});

it('renders the attribution footer when configured', function () {
    config(['nexo.attribution_text' => 'powered by alvarocdev.com', 'nexo.attribution_url' => 'https://alvarocdev.com']);

    $this->get('/')->assertSee('powered by alvarocdev.com');
});
