<?php

// The scheduler is the hub's only background worker on shared hosting
// (routes/console.php): it drains the mail queue and prunes the back-channel
// revocations. Nothing else would notice if either line went missing — the
// app keeps answering while mails pile up in `jobs` and revocations never go.

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;

function scheduledEvent(string $name): ?Event
{
    return collect(app(Schedule::class)->events())
        ->first(fn (Event $event): bool => $event->description === $name);
}

it('drains the mail queue every minute', function () {
    $drain = scheduledEvent('queue-drain');

    expect($drain)->not->toBeNull()
        ->and($drain->expression)->toBe('* * * * *');
});

it('prunes the expired back-channel revocations every day, inline', function () {
    // nexo-sso-client v2: revocations live 24 h; nexo-sso:prune deletes them.
    $prune = scheduledEvent('nexo-sso-prune');

    expect($prune)->not->toBeNull()
        ->and($prune->expression)->toBe('0 0 * * *');

    DB::table('nexo_sso_revocations')->insert([
        ['sid' => 'old-sid', 'sub' => null, 'revoked_at' => now()->subDays(2), 'expires_at' => now()->subDay()],
        ['sid' => 'live-sid', 'sub' => null, 'revoked_at' => now(), 'expires_at' => now()->addDay()],
    ]);

    $prune->run(app());

    expect(DB::table('nexo_sso_revocations')->pluck('sid')->all())->toBe(['live-sid']);
});
