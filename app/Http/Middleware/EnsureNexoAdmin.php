<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Role gate for /admin: the request must be authenticated AND the user's Nexo ID
 * `sub` must be in the nexo.admin_subs allowlist. Anyone else — guest or ordinary
 * user — gets a flat 403 (no login redirect, no enumeration). With no admin_subs
 * configured (default) nobody passes, so a standalone install has no admin
 * surface. (AC-ADMIN-1)
 */
class EnsureNexoAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $sub = $user !== null ? (string) $user->getAttribute('nexo_id_sub') : '';
        $admins = (array) config('nexo.admin_subs', []);

        abort_unless($sub !== '' && in_array($sub, $admins, true), 403);

        return $next($request);
    }
}
