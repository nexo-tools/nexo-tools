<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

// Help center. FAQ items are translatable: each tool defines its own list in
// lang/<locale>/help.php as `faqs => [['q' => ..., 'a' => ...], ...]`. Contact
// target comes from the tool (support email / form route).
class HelpController extends Controller
{
    public function __invoke(): View
    {
        return view('help.index', [
            'faqs' => (array) __('help.faqs'),
            'contactUrl' => config('nexo.support_url') ?: 'mailto:'.config('nexo.support_email', ''),
        ]);
    }
}
