# Deployment — NexoTools (ecosystem hub) on shared hosting (LiteSpeed)

NexoTools is the Laravel 13 hub of the Nexo ecosystem on Hostinger shared hosting, served from `nexotools.alvarocdev.com` via a symlink to `public/`. The tool registry that drives the landing, the app-switcher and the beacon allowlist is `config/nexo-ecosystem.php`. Placeholders: `<domain>` (the hosting account's domain folder, e.g. `alvarocdev.com`).

> **Database — read this before provisioning.** The v1 runbook below provisions **SQLite**, which
> was right when the hub was read-only content. It is no longer: v2 added accounts, the *your
> tools* springboard (`user_tools`) and the beacon receiver (`beacon_events`), all of which write
> on every request. Local development runs on **MySQL** (the shared `nexo` stack). SQLite serialises
> writes on a single file, so the beacon under concurrency is the wrong shape for it. **New
> instances should provision MySQL**; the SQLite steps are kept because the running instance was
> deployed with them and needs to be checked before it is migrated.

Assumptions: SSH + Composer over SSH; **no Node on the server** — assets are built locally/CI and uploaded.

## Running it locally

Before deploying anywhere, this is how to get Nexo Tools up on your own machine. The README
points here on purpose: keeping the steps in one place is why they stopped drifting.

### Option A — everything in Docker (recommended if you just want it running)

`compose.yaml` in this repo runs the **app only**: the author's machine keeps a single
MySQL/Mailpit shared by every Nexo tool, so shipping another database per repo would be
waste. `compose.selfhost.yaml` is the overlay that fills the gap for everyone else.

```sh
cp .env.example .env
# in .env: DB_HOST=mysql  DB_PORT=3306  MAIL_HOST=mailpit  MAIL_PORT=1025
docker compose -f compose.yaml -f compose.selfhost.yaml up -d
docker compose exec laravel.test composer install
docker compose exec laravel.test php artisan key:generate
docker compose exec laravel.test php artisan migrate
npm install && npm run build
```

The app answers on **http://localhost:8080** and outgoing mail lands in Mailpit at
http://localhost:8025.

### Option B — your own MySQL

Keep `compose.yaml` alone (or no Docker at all) and point `.env` at your database:
`DB_HOST` / `DB_PORT` / `DB_DATABASE` (`nexotools`) / `DB_USERNAME` / `DB_PASSWORD`. Everything
else is a stock Laravel app: `composer install`, `php artisan key:generate`,
`php artisan migrate`, `npm run build`, `php artisan serve`.

> The values committed in `.env.example` target the author's shared local stack
> (`host.docker.internal:3307`). Override them — they are a default, not a requirement.

Run the suite with `vendor/bin/pest` (SQLite in memory — it never touches your database).

---

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
#   SESSION_DRIVER=file, CACHE_STORE=file
#   Mail — a production instance sends real mail (today: the password reset):
#     MAIL_MAILER=smtp + the mailbox's host/port (this app reads MAIL_SCHEME,
#     not MAIL_ENCRYPTION — see config/mail.php:42), and the family From
#     convention: one address per tool,
#     MAIL_FROM_ADDRESS="nexotools@<domain>" / MAIL_FROM_NAME="Nexo Tools".
#     MAIL_MAILER=log belongs to an instance that deliberately sends nothing:
#     with it the reset link never leaves the box, it only lands in the log.
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
