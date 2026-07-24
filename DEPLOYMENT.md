# Deployment — NexoTools (ecosystem hub) on shared hosting (LiteSpeed)

NexoTools is a small Laravel 13 hub (mostly static content driven by `config/tools.php`) on Hostinger shared hosting, served from `nexotools.alvarocdev.com` via a symlink to `public/`. It uses **SQLite** (not MySQL) — a hub with no write-heavy features doesn't need a DB server; v2 ("your tools" behind Nexo ID) can move to MySQL if it ever needs to. Placeholders: `<domain>` (the hosting account's domain folder, e.g. `alvarocdev.com`).

Assumptions: SSH + Composer over SSH; **no Node on the server** — assets are built locally/CI and uploaded.

## First deploy (over SSH)

```bash
# 1. Code
cd ~/domains/<domain>
git clone <repo> nexo-tools && cd nexo-tools
composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
php artisan package:discover --ansi

# 2. .env
cp .env.example .env
#   APP_NAME="NexoTools", APP_ENV=production, APP_DEBUG=false
#   APP_URL=https://nexotools.<domain>
#   DB_CONNECTION=sqlite   +   DB_DATABASE=<abs path>/database/database.sqlite
#   SESSION_DRIVER=file, CACHE_STORE=file, MAIL_MAILER=log
#   NEXO_ATTRIBUTION_TEXT / NEXO_ATTRIBUTION_URL for the footer
php artisan key:generate --force

# 3. SQLite DB
touch database/database.sqlite
php artisan migrate --force

# 4. storage symlink (storage:link fails — exec() disabled)
ln -s "$PWD/storage/app/public" "$PWD/public/storage"

# 5. Assets — built locally and uploaded (no Node on the server), FROM YOUR MACHINE:
#   npm install && npm run build
#   rsync -az --delete public/build/ <ssh-host>:domains/<domain>/nexo-tools/public/build/

# 6. Production caches (route:cache is safe here — no closure routes)
php artisan config:cache && php artisan route:cache && php artisan view:cache

# 7. Point the subdomain at public/ (hPanel seeds a default dir here first)
cd ~/domains/<domain>
rm -rf public_html/nexotools
ln -s ~/domains/<domain>/nexo-tools/public public_html/nexotools
```

## Gotchas (learned deploying 2026-07-23)

- **`bootstrap/cache/*.php` must NOT be in git.** A committed `packages.php` from a local build lists `laravel/pail` (a **dev** dependency); under `composer install --no-dev` the class is absent and *every* artisan command + the app boot crash with `Class "Laravel\Pail\PailServiceProvider" not found`. Fixed: `bootstrap/cache/.gitignore` now ignores `*`. If it recurs on a clone: `rm -f bootstrap/cache/*.php && php artisan package:discover`.
- **`storage/framework/{sessions,views,cache/data}` and `storage/logs` must exist.** A clone without them 500s on the first request (`file_put_contents(...sessions/...): No such file or directory`) because `SESSION_DRIVER=file` can't write. Fixed: those dirs now carry a tracked `.gitignore`. If missing: `mkdir -p storage/framework/{sessions,views,cache/data} storage/logs`.
- **SQLite `DB_DATABASE`** must be the absolute path to `database/database.sqlite` (the `.env.example` value from the sibling clone points at a MySQL name — override it).
- **`--no-scripts`** on composer (shared hosts disable `proc_open`); run `package:discover` manually.
- Strict CSP is re-asserted in `public/.htaccess` (LiteSpeed overrides the PHP-sent one) — kept in sync with the middleware by a test.

## Updates

```bash
cd ~/domains/<domain>/nexo-tools
php artisan down --retry=15 || true
git pull origin main
composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
php artisan package:discover --ansi
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan up
# then, from your machine: rsync the fresh public/build (see step 5)
```
