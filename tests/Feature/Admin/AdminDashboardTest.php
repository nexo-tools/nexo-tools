<?php

use App\Models\BeaconEvent;
use App\Models\User;

const ADMIN_SUB = 'admin-sub-123';

/** A user whose Nexo ID sub is in the admin allowlist. */
function adminUser(): User
{
    config(['nexo.admin_subs' => [ADMIN_SUB]]);
    $user = User::factory()->create();
    $user->forceFill(['nexo_id_sub' => ADMIN_SUB])->save();

    return $user;
}

function seedEvent(array $attributes = []): BeaconEvent
{
    return BeaconEvent::query()->create(array_merge([
        'origin' => 'nexolinks',
        'path' => '/',
        'visitor_hash' => str_pad((string) fake()->randomNumber(), 64, '0'),
        'day' => now()->toDateString(),
    ], $attributes));
}

it('AC-ADMIN-1: 403s guests and non-admins, and lets an allowlisted sub in', function () {
    config(['nexo.admin_subs' => [ADMIN_SUB]]);

    // Guest: no admin surface.
    $this->get('/admin')->assertForbidden();

    // Signed-in but not in the allowlist.
    $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();

    // Signed-in with an allowlisted sub.
    $admin = User::factory()->create();
    $admin->forceFill(['nexo_id_sub' => ADMIN_SUB])->save();
    $this->actingAs($admin)->get('/admin')->assertOk();
});

it('AC-ADMIN-1: with no admin_subs configured (standalone), nobody may enter', function () {
    config(['nexo.admin_subs' => []]);

    $user = User::factory()->create();
    $user->forceFill(['nexo_id_sub' => ADMIN_SUB])->save();

    $this->actingAs($user)->get('/admin')->assertForbidden();
});

it('AC-ADMIN-2: shows aggregated visits, unique visitors, per-origin and top paths', function () {
    $admin = adminUser();

    // 3 nexolinks visits from 2 distinct visitors; /pricing is the top path.
    seedEvent(['visitor_hash' => str_repeat('a', 64), 'path' => '/pricing']);
    seedEvent(['visitor_hash' => str_repeat('a', 64), 'path' => '/pricing']);
    seedEvent(['visitor_hash' => str_repeat('b', 64), 'path' => '/about']);

    $response = $this->actingAs($admin)->get('/admin');

    $response->assertOk()
        ->assertSee('Nexo Links')
        ->assertSee('/pricing');

    expect($response->viewData('totalVisits'))->toBe(3)
        ->and($response->viewData('totalUniques'))->toBe(2);

    $byOrigin = collect($response->viewData('byOrigin'));
    expect($byOrigin->firstWhere('origin', 'nexolinks')->visits)->toBe(3);

    // The busiest path leads.
    expect(collect($response->viewData('topPaths'))->first()->path)->toBe('/pricing');
});

it('AC-ADMIN-3: the alvarocdev view counts its visits and which tool referred them', function () {
    $admin = adminUser();

    seedEvent(['origin' => 'alvarocdev', 'ref' => 'nexolinks', 'visitor_hash' => str_repeat('c', 64)]);
    seedEvent(['origin' => 'alvarocdev', 'ref' => 'nexolinks', 'visitor_hash' => str_repeat('d', 64)]);
    seedEvent(['origin' => 'alvarocdev', 'ref' => null, 'visitor_hash' => str_repeat('e', 64)]);

    $response = $this->actingAs($admin)->get('/admin');

    $response->assertOk()->assertSee(__('Visitas a alvarocdev'));

    expect($response->viewData('alvaroVisits'))->toBe(3);

    $refs = collect($response->viewData('alvaroRefs'));
    expect($refs->firstWhere('ref', 'nexolinks')->visits)->toBe(2);
});

it('AC-ADMIN-4: with no data it renders an empty state, not an error', function () {
    $admin = adminUser();

    $this->actingAs($admin)->get('/admin')
        ->assertOk()
        ->assertSee(__('Aún no hay datos de visitas.'));
});
