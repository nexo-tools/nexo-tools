<?php

// Guardian: brand colors come from nexo-brand tokens (var(--nexo-*)), never raw
// hex in Blade views or app CSS. Copy into tests/Feature/Nexo/. Adjust $allowed
// for files that legitimately hold literal colors (the generated tokens file, an
// inlined SVG mark partial). SVGs under public/ are not scanned.

use RecursiveDirectoryIterator as Dir;
use RecursiveIteratorIterator as Walk;

it('has no hardcoded hex colors in blade views or app css (use --nexo-* tokens)', function () {
    $roots = array_filter([resource_path('views'), resource_path('css')], 'is_dir');

    // Filenames allowed to contain literal hex: the generated brand tokens, the
    // shared chrome layer, and head's PWA theme-color meta (a <meta> content
    // value can't reference a CSS var).
    $allowed = ['nexo-tokens.css', 'nexo-ui.css', 'head.blade.php', 'nexo-seo.blade.php'];

    $offenders = [];
    foreach ($roots as $root) {
        foreach (new Walk(new Dir($root, FilesystemIterator::SKIP_DOTS)) as $file) {
            if (! preg_match('/\.(blade\.php|css)$/', $file->getFilename())) {
                continue;
            }
            if (in_array($file->getFilename(), $allowed, true)) {
                continue;
            }
            $contents = file_get_contents($file->getPathname());
            if (preg_match_all('/#[0-9a-fA-F]{3,8}\b/', $contents, $m)) {
                $offenders[] = $file->getPathname().' -> '.implode(', ', array_unique($m[0]));
            }
        }
    }

    expect($offenders)->toBe([], "Hardcoded hex colors found (use var(--nexo-*)):\n".implode("\n", $offenders));
});
