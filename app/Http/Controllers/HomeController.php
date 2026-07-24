<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    /** The public ecosystem hub (v1 showcase — no account needed). */
    public function __invoke(): View
    {
        /** @var array<string, array<string, mixed>> $all */
        $all = config('nexo-ecosystem.tools', []);
        $current = config('nexo-ecosystem.current');

        // The hub lists the ecosystem tools except itself (the current one).
        $tools = array_filter($all, fn (string $key): bool => $key !== $current, ARRAY_FILTER_USE_KEY);

        return view('home', [
            'tools' => $tools,
            'githubOrg' => config('nexo-ecosystem.github_org_url'),
        ]);
    }
}
