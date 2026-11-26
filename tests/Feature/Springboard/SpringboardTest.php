<?php

// «Tus herramientas» (/app) is the light launcher of CONTRATO-F3 D7: every tool
// of the registry, always, each opening its own panel already signed in, plus
// who is signed in. It replaced a springboard that started empty and had to be
// filled by hand (H-nexotools-06).

use App\Models\User;
use App\Models\UserTool;
use Illuminate\Support\Facades\Http;

function launcherSsoOn(): void
{
    config()->set('nexo-sso.enabled', true);
    config()->set('nexo-sso.issuer', 'https://id.example.test');
}

it('lists every tool of the registry with the URL that opens its panel', function () {
    launcherSsoOn();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/app')->assertOk();
    $html = $response->getContent();

    $registry = (array) config('nexo-ecosystem.tools');
    expect($registry)->toHaveCount(6);

    foreach ($registry as $key => $tool) {
        $response->assertSee('data-launcher-tool="'.$key.'"', false)->assertSee($tool['name']);

        if ($key === config('nexo-ecosystem.current')) {
            // The hub itself: this very page, no «Abrir» that only reloads it.
            expect($html)->toMatch('/data-launcher-tool="'.$key.'"\s+aria-current="page"/');

            continue;
        }

        // url + entry: the tools' /auth/nexo/enter (0 clicks with a Nexo ID
        // session), Nexo ID's /profile.
        $href = rtrim($tool['url'], '/').'/'.ltrim($tool['entry'], '/');
        expect($html)->toContain('href="'.$href.'" class="nexo-btn nexo-btn--primary nexo-btn--sm" data-launcher-open');
    }

    expect($html)->toContain('href="'.rtrim($registry['nexoid']['url'], '/').'/profile"')
        ->and(substr_count($html, 'data-launcher-open'))->toBe(5);
});

it('lists the whole registry from the very first visit, with nothing to add by hand', function () {
    launcherSsoOn();
    $user = User::factory()->create();

    $html = $this->actingAs($user)->get('/app')->assertOk()->getContent();

    expect(substr_count($html, 'data-launcher-tool='))->toBe(count((array) config('nexo-ecosystem.tools')))
        ->and($html)->not->toContain('/app/tools')
        ->and($html)->not->toContain(__('Add tools'));
});

it('shows who is signed in and their Nexo ID account', function () {
    launcherSsoOn();
    $user = User::factory()->create(['name' => 'Ana Vidal', 'email' => 'ana@example.test']);

    $this->actingAs($user)->get('/app')
        ->assertOk()
        ->assertSee('data-launcher-name>Ana Vidal', false)
        ->assertSee('data-launcher-email>ana@example.test', false)
        ->assertSee('href="https://id.example.test/profile" class="nexo-btn nexo-btn--ghost nexo-btn--sm" data-launcher-nexo-id>'.__('nexo.account.nexo_id'), false);
});

it('opens the tools\' landings and offers no Nexo ID account when SSO is off', function () {
    // Standalone: there is no shared session to ride, so each tool opens as a
    // visitor — the same rule as the switcher (NexoChrome::switcherHref).
    config()->set('nexo-sso.enabled', false);
    $user = User::factory()->create();

    $html = $this->actingAs($user)->get('/app')->assertOk()->getContent();

    $events = config('nexo-ecosystem.tools.nexoevents');
    expect($html)->toContain('href="'.rtrim($events['url'], '/').'" class="nexo-btn nexo-btn--primary nexo-btn--sm" data-launcher-open')
        ->and($html)->not->toContain('/auth/nexo/enter')
        ->and($html)->not->toContain('data-launcher-nexo-id')
        // …and it doesn't promise «this same account» it can't keep.
        ->and($html)->toContain('data-launcher-lead>'.e(__('Every Nexo tool, one click away.')))
        ->and($html)->not->toContain(e(__('Open any Nexo tool: you go in with this same account.')));

    launcherSsoOn();
    expect($this->actingAs($user)->get('/app')->getContent())
        ->toContain('data-launcher-lead>'.e(__('Open any Nexo tool: you go in with this same account.')));
});

it('badges a tool that is not live yet instead of opening it', function () {
    launcherSsoOn();
    config()->set('nexo-ecosystem.tools.nexoshort.status', 'soon');
    $user = User::factory()->create();

    $html = $this->actingAs($user)->get('/app')->assertOk()->getContent();

    $card = substr($html, strpos($html, 'data-launcher-tool="nexoshort"'));
    $card = substr($card, 0, strpos($card, '</li>'));

    expect($card)->toContain(__('Coming soon'))->not->toContain('data-launcher-open');
});

it('keeps the springboard rows it no longer reads, and its old write routes are gone', function () {
    // «Sin borrar tablas ni datos»: the user_tools table and every row in it
    // stay exactly as they were; the launcher simply doesn't depend on them.
    $user = User::factory()->create();
    $user->userTools()->create(['tool_key' => 'nexolinks']);
    $before = UserTool::query()->orderBy('id')->get(['id', 'user_id', 'tool_key', 'created_at'])->toArray();

    $this->actingAs($user)->get('/app')->assertOk();
    $this->actingAs($user)->post('/app/tools', ['tool_key' => 'nexoevents'])->assertStatus(405);
    $this->actingAs($user)->delete('/app/tools/nexolinks')->assertStatus(405);

    expect(UserTool::query()->orderBy('id')->get(['id', 'user_id', 'tool_key', 'created_at'])->toArray())->toBe($before);
});

it('is local: the launcher never calls another tool', function () {
    Http::fake();
    launcherSsoOn();
    $user = User::factory()->create();

    $this->actingAs($user)->get('/app')->assertOk();

    Http::assertNothingSent();
});

it('needs a session', function () {
    $this->get('/app')->assertRedirect(route('login'));
});
