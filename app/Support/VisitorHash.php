<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Anonymous, daily-rotating visitor fingerprint. Derived from the app key, the
 * current date, the IP and the User-Agent — none of which are stored. The date
 * in the payload makes it impossible to link the same visitor across days, so
 * "unique visitors per day" is possible while re-identification is not.
 *
 * Canonical cookieless pattern, adapted from the nexo-links VisitorHash (CATALOG).
 */
class VisitorHash
{
    public static function make(Request $request): string
    {
        return hash('sha256', implode('|', [
            (string) config('app.key'),
            now()->toDateString(),
            (string) $request->ip(),
            (string) $request->userAgent(),
        ]));
    }
}
