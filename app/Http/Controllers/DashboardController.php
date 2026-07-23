<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    /** "Your tools" (v2 seed): once signed in, jump to any Nexo tool with one account. */
    public function __invoke(): View
    {
        return view('app.dashboard', ['tools' => config('tools.tools')]);
    }
}
