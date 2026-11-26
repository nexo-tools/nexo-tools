<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Attach security headers and a self-contained Content-Security-Policy to
     * every web response. The policy allows only same-origin resources (no CDNs,
     * no external fonts) — matching the project's zero-external-requests rule.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        foreach ($this->headers($request) as $name => $value) {
            $response->headers->set($name, $value);
        }

        return $response;
    }

    /** @return array<string, string> */
    private function headers(Request $request): array
    {
        $headers = [
            'Content-Security-Policy' => $this->contentSecurityPolicy(),
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=(), interest-cohort=()',
            'Cross-Origin-Opener-Policy' => 'same-origin',
            'X-XSS-Protection' => '0',
        ];

        // Instruct browsers to stay on HTTPS once the site is served securely.
        if ($request->secure()) {
            $headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
        }

        return $headers;
    }

    private function contentSecurityPolicy(): string
    {
        // Alpine evaluates directive expressions at runtime, which needs
        // 'unsafe-eval'; inline styles power per-business theming and stat bars.
        // The only inline <script> is the FOUC-free theme-init (partials/theme-init);
        // it is allow-listed by its exact sha256 hash — no 'unsafe-inline' for scripts.
        // If you edit that snippet, recompute the hash (and mirror it in public/.htaccess).
        $script = "'self' 'unsafe-eval' 'sha256-QY4re+NFw+ChK0c8H/EaTpktoUisSWU0fL7V6J43umM='";
        $style = "'self' 'unsafe-inline'";
        $connect = "'self'";

        // Allow the Vite dev server (and its websocket) while running HMR locally.
        if ($dev = $this->viteDevServer()) {
            $script .= " {$dev}";
            $style .= " {$dev}";
            $connect .= " {$dev} ".preg_replace('#^http#', 'ws', $dev);
        }

        return implode('; ', [
            "default-src 'self'",
            "base-uri 'self'",
            "form-action {$this->formAction()}",
            "frame-ancestors 'none'",
            "object-src 'none'",
            "img-src 'self' data:",
            "font-src 'self'",
            "script-src {$script}",
            "style-src {$style}",
            "connect-src {$connect}",
        ]);
    }

    /**
     * `form-action` also governs the redirects that FOLLOW a form POST. With SSO
     * on, «Cerrar sesión» posts here and is redirected to Nexo ID's end_session:
     * with 'self' alone the browser silently dropped that hop (Chrome and Safari),
     * so nothing happened on screen and Nexo ID stayed signed in (H-nexotools-01).
     * The issuer's origin is allowed only while SSO is on; standalone stays
     * same-origin. public/.htaccess carries the production issuer
     * (CspFormActionTest + the sync test in SecurityHeadersTest).
     */
    private function formAction(): string
    {
        $issuer = (string) config('nexo-sso.issuer', '');
        if (! config('nexo-sso.enabled') || $issuer === '') {
            return "'self'";
        }

        $parts = parse_url($issuer);
        if (! is_array($parts) || empty($parts['host'])) {
            return "'self'";
        }

        $origin = ($parts['scheme'] ?? 'https').'://'.$parts['host'].(isset($parts['port']) ? ':'.$parts['port'] : '');

        return "'self' {$origin}";
    }

    private function viteDevServer(): ?string
    {
        $hotFile = public_path('hot');

        if (! app()->environment('local') || ! is_file($hotFile)) {
            return null;
        }

        $url = trim((string) file_get_contents($hotFile));

        return $url === '' ? null : rtrim($url, '/');
    }
}
