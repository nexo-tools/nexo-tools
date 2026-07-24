<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * "Your tools" springboard. A signed-in user curates the ecosystem tools they
 * use and launches them. Everything is LOCAL (the user_tools pivot resolved
 * against the registry) — no cross-tool API calls in v1. (AC-TOOLS-1..5)
 */
class SpringboardController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        /** @var array<string, array<string, mixed>> $registry */
        $registry = config('nexo-ecosystem.tools', []);

        // Only this user's tools (isolation), newest first. (AC-TOOLS-5)
        $addedKeys = $user->userTools()->orderByDesc('created_at')->pluck('tool_key')->all();

        // Resolve added keys against the registry; skip any tool since removed
        // from it. Purely local — no cross-tool requests. (AC-TOOLS-4)
        $added = [];
        foreach ($addedKeys as $key) {
            if (isset($registry[$key])) {
                $added[$key] = $registry[$key] + ['key' => $key];
            }
        }

        // Everything else in the registry is offered to add.
        $available = [];
        foreach ($registry as $key => $tool) {
            if (! isset($added[$key])) {
                $available[$key] = $tool + ['key' => $key];
            }
        }

        return view('app.dashboard', [
            'added' => $added,
            'available' => $available,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Only real registry keys may be added. (AC-TOOLS-3)
            'tool_key' => ['required', 'string', Rule::in(array_keys((array) config('nexo-ecosystem.tools')))],
        ]);

        /** @var User $user */
        $user = $request->user();

        // Idempotent + unique per (user, tool). (AC-TOOLS-2)
        $user->userTools()->firstOrCreate(['tool_key' => $validated['tool_key']]);

        return redirect()->route('dashboard');
    }

    public function destroy(Request $request, string $tool): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        // Scoped to the current user — never touches another user's tools. (AC-TOOLS-5)
        $user->userTools()->where('tool_key', $tool)->delete();

        return redirect()->route('dashboard');
    }
}
