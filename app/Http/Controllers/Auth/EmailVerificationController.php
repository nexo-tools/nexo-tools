<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Email verification for hub accounts.
 *
 * Deliberately NOT enforced as middleware on the panel: somebody who just
 * signed up has tools to add, and locking them out over an unread email would
 * be the app's problem, not theirs. What verification buys is the reset path —
 * a typo in the address means the only way back into the account is gone, and
 * until now nothing in the product would ever have said so. It also closes the
 * hole where anybody could register somebody else's address.
 */
class EmailVerificationController extends Controller
{
    public function notice(Request $request): View|RedirectResponse
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->route('dashboard')
            : view('auth.verify-email');
    }

    /** Signed + throttled by the route; the request class checks id/hash against the user. */
    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->route('dashboard')->with('status', __('Your email is verified.'));
    }

    public function send(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', __('We have resent the verification link.'));
    }
}
