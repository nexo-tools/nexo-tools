<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    /** The public ecosystem hub (v1 showcase — no account needed). */
    public function __invoke(): View
    {
        return view('home', [
            'tools' => config('tools.tools'),
            'githubOrg' => config('tools.github_org'),
        ]);
    }
}
