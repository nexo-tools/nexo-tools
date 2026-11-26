<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\NexoChrome;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * «Tus herramientas» — the hub's panel as a light launcher (CONTRATO-F3 D7).
 *
 * Every tool of the registry, always, in the registry's order, each opening
 * its own panel already signed in (`url + entry`, the same link the switcher
 * uses: NexoChrome::switcherHref), plus who is signed in and their Nexo ID
 * account. «Cerrar sesión» stays in the account menu of the header and in the
 * phone sheet (AccountMenuTest: no sign-out form outside the chrome).
 *
 * Until 2026-11-26 this was a springboard the person had to fill by hand: it
 * started empty, offered to add the hub itself and Nexo ID, and its «Abrir»
 * went to each tool's landing as a visitor (H-nexotools-06). It could only
 * ever hold registry tools, so there is nothing of the person's own to keep
 * apart: the hand-made list is gone, and the `user_tools` rows it wrote stay
 * in the database untouched (no table or data is dropped; UserTool remains).
 * Purely local — no cross-tool requests.
 */
class SpringboardController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        /** @var array<string, array<string, mixed>> $registry */
        $registry = (array) config('nexo-ecosystem.tools', []);
        $current = NexoChrome::current();

        $tools = [];
        foreach ($registry as $key => $tool) {
            $live = ($tool['status'] ?? 'live') === 'live' && (string) ($tool['url'] ?? '') !== '';

            $tools[$key] = [
                'key' => $key,
                'name' => (string) ($tool['name'] ?? $key),
                'mark' => (string) ($tool['mark'] ?? '/favicon.svg'),
                'tagline' => NexoChrome::tagline($key, $tool),
                'live' => $live,
                'current' => $key === $current,
                'href' => $live ? NexoChrome::switcherHref($key, $tool) : null,
            ];
        }

        return view('app.dashboard', [
            'user' => $user,
            'tools' => $tools,
            'nexoIdAccountUrl' => NexoChrome::nexoIdAccountUrl(),
        ]);
    }
}
