<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;

it('AC-TOOLS-1: shows the added tools as launch cards, not the empty state', function () {
    $user = User::factory()->create();
    $user->userTools()->create(['tool_key' => 'nexolinks']);

    $response = $this->actingAs($user)->get('/app');

    $response->assertOk()
        ->assertSee('Nexo Links')
        ->assertSee(config('nexo-ecosystem.tools.nexolinks.url'), false) // launch url
        ->assertSee(__('Abrir'))
        ->assertDontSee(__('Todavía no añadiste herramientas'));

    // Precisely: the added set resolves the pivot against the registry.
    expect($response->viewData('added'))->toHaveKey('nexolinks');
});

it('AC-TOOLS-2: adds a tool, keeps it unique per user, and removes it', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/app/tools', ['tool_key' => 'nexolinks'])
        ->assertRedirect(route('dashboard'));
    $this->assertDatabaseHas('user_tools', ['user_id' => $user->id, 'tool_key' => 'nexolinks']);

    // Idempotent: adding the same tool again does not duplicate the row.
    $this->actingAs($user)->post('/app/tools', ['tool_key' => 'nexolinks']);
    $this->assertDatabaseCount('user_tools', 1);

    // Remove it.
    $this->actingAs($user)->delete('/app/tools/nexolinks')
        ->assertRedirect(route('dashboard'));
    $this->assertDatabaseMissing('user_tools', ['user_id' => $user->id, 'tool_key' => 'nexolinks']);
});

it('AC-TOOLS-3: rejects a tool_key that is not in the registry', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from('/app')
        ->post('/app/tools', ['tool_key' => 'not-a-real-tool'])
        ->assertSessionHasErrors('tool_key');

    $this->assertDatabaseCount('user_tools', 0);
});

it('AC-TOOLS-4: a user with no tools sees the empty state and no cross-tool API call is made', function () {
    Http::fake(); // any outbound HTTP would be recorded

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/app');

    $response->assertOk()->assertSee(__('Todavía no añadiste herramientas'));
    expect($response->viewData('added'))->toBe([]);

    // Purely local: the springboard never reaches out to another tool. (v1)
    Http::assertNothingSent();
});

it('AC-TOOLS-5: a user never sees or removes another user\'s tools', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();
    $alice->userTools()->create(['tool_key' => 'nexolinks']);

    // Bob's springboard does not include Alice's tool.
    $bobView = $this->actingAs($bob)->get('/app');
    expect($bobView->viewData('added'))->not->toHaveKey('nexolinks');

    // Bob cannot delete Alice's tool — the delete is scoped to the actor.
    $this->actingAs($bob)->delete('/app/tools/nexolinks');
    $this->assertDatabaseHas('user_tools', ['user_id' => $alice->id, 'tool_key' => 'nexolinks']);
});
