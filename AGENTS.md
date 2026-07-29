# Nexo Tools

> Entry point for any AI/agent working on this project. It follows Alvaro's standards system (repo `alvaro`, alvarocdev.com). Keep this file updated: persist here the important context that comes up during work sessions.
> This repo is **public**: no secrets, credentials or sensitive infrastructure details here.

## What this project is

The hub of the Nexo ecosystem, written for **non-technical** visitors: what each tool does, how to
use it, and a direct way in. It also carries the ecosystem's shared identity — it is the **living
reference** of the Nexo brand and chrome (header, app-switcher, footer, tokens, `/help`) that the
sibling tools copy.

**Current state: LIVE at https://nexotools.alvarocdev.com.** Both layers are built and deployed:

- **v1 — the public hub**: landing with the tool grid, `/help`, i18n es/en/pt, legal pages.
- **v2 — the account layer**: optional Nexo ID SSO, the personal *"your tools"* springboard
  (`user_tools`), the cookieless beacon receiver and the `/admin` metrics dashboard.

The tool registry behind the landing, the app-switcher and the beacon allowlist is
`config/nexo-ecosystem.php` — the same block copied across the six tools.

## Stack

Laravel 13 · PHP 8.3+ · Blade + Alpine.js + Tailwind CSS (Vite) · MySQL (SQLite in tests).
Zero external runtime requests: system fonts, inline SVG, no CDNs.
Quality gate: Pint + Larastan (level 6) + `composer audit` + translations `--check` + Pest.

## How to run it

No local PHP/Composer — everything through Docker. Stateful services (MySQL, Mailpit, phpMyAdmin)
are **not** in this repo's `compose.yaml`: they run once for the whole ecosystem in the shared dev
environment (`~/dev-environment`, compose project `nexo`) and this app reaches them over
`host.docker.internal`.

```bash
cd ~/dev-environment && docker compose up -d mysql mailpit   # shared, once per session
cd ~/nexotools
docker run --rm -v "$PWD":/app -w /app composer:latest install
docker compose up -d                                          # app on http://localhost:8080
docker compose exec laravel.test php artisan migrate
npm install && npm run build

# the CI gate
docker run --rm -v "$PWD":/app -w /app composer:latest \
  sh -c 'vendor/bin/pint --test && vendor/bin/phpstan analyse && vendor/bin/pest'
```

| What | Where |
|---|---|
| App | http://localhost:8080 (`APP_PORT`) |
| Vite | port 5173 (`VITE_PORT`) |
| MySQL | `host.docker.internal:3307`, db `nexo_tools` |
| Mailpit | SMTP 1025 · UI http://localhost:8025 |

Ports are fixed per tool (map in `alvaro/templates/dev-environment/README.md`); 3306 and 8081
belong to the unrelated `work` stack. Anyone without that shared stack can use
`compose.selfhost.yaml`, which adds MySQL and Mailpit — see [DEPLOYMENT.md](DEPLOYMENT.md).

## Production

**Live at https://nexotools.alvarocdev.com** (Hostinger shared hosting, subdomain symlinked to
`public/`). **Auto-deploy on every push to `main`** (`deploy.yml`, `concurrency: production`), so
the suite must be green *before* pushing — the push publishes. Manual trigger:
`gh workflow run deploy.yml --repo nexo-tools/nexo-tools`. Hostinger's SSH drops connections when
several repos deploy at once (the whole ecosystem shares one account), so the rsync and remote-script
steps retry three times with backoff — three failures in a row mean something real.

## Project conventions

- The project runs on the `planning-by-stages` skill: `docs/PLAN.md` (governing), `docs/adr/`
  (decisions), `docs/SCOPE.md`. One task at a time; commits `"N,M description"`.
- **Language**: this file and the README are in **English**, as a public repo requires. `docs/`
  (PLAN, SCOPE, the ADRs) is still **Spanish**, written while the repo was private. The ADRs are
  immutable decision records — translating them would rewrite the record — so they stay as they
  are; **anything new under `docs/` is written in English**. Communication with Alvaro is Spanish.
- No technical jargon in hub content; the single technical mention is the "developer?" link to the
  GitHub org.
- The hub does not duplicate each tool's landing (anti-cannibalisation SEO, ADR-001).
- Attribution defaults to the **product** (`made with Nexo Tools` → the GitHub org), never to
  alvarocdev.com: a third-party instance must not advertise the upstream author
  (`add-branding-footer`). Alvaro's instances set `NEXO_ATTRIBUTION_*` in their own `.env`.

## Static pages (nexo-ui standard)

- **Errors** 403/404/419/429/500/503 in `resources/views/errors/`, all on the `error-layout`
  component (chrome + theme + i18n). Adding a code is one line.
- **Legal**: `/privacidad` and `/terminos` (route names `legal.privacy` / `legal.terms`) →
  `LegalController` + `resources/views/legal/show.blade.php`. The text lives in
  `lang/{es,en,pt}/legal.php` because these are paragraphs, not loose strings: **Spanish is the
  source**, en/pt are translations of it. Linked from the `nexo-footer` (so from every page,
  error pages included) and present in `sitemap.xml`.
- The content describes what the code actually does (accounts, the *your tools* panel, optional
  SSO, the cookieless beacon). If what gets stored changes, the text changes in all three
  languages.
- **Instance operator and contact**: `NEXO_LEGAL_OPERATOR` / `NEXO_LEGAL_CONTACT` (config
  `nexo.legal.*`). With no values that section is not rendered, so a clone never publishes the
  upstream author's details. **Still to be set in this instance's production `.env`.**
- Guardians: `tests/Feature/Nexo/StaticPagesTest.php`, `BrandAssetsPresentTest.php`,
  `DarkModeCoverageTest.php`.

## Beacon (cookieless ecosystem analytics, opt-in)

The hub ingests pageviews from the tools and alvarocdev via `POST /beacon`. **Off by default**
(`NEXO_BEACON_ENABLED=false`): without it `/beacon` answers 204 and writes nothing.

- **Privacy**: cookieless, no IP/UA/PII persisted. Only a daily anonymous `visitor_hash` (SHA-256
  of app key + date + IP + UA — nothing raw, the canonical `VisitorHash` pattern from nexolinks),
  `origin` (tool slug), `path` truncated to 255, `day`, `country` (ISO-2 from an edge header if
  present) and `ref` (referring tool slug, for cross-tool attribution). Honours `DNT`/`Sec-GPC`.
  Never sets a cookie.
- **Allowlist**: only accepts `origin`/`ref` ∈ keys of `config('nexo.beacon.origins')` (derived
  from `nexo-ecosystem` + alvarocdev). CORS answers only those hosts and handles the OPTIONS
  preflight. Per-IP rate limit (`NEXO_BEACON_RATE_LIMIT`, 60/min).
- **Route outside the `web` group** (no session → no `Set-Cookie`, no CSRF): `routes/beacon.php`,
  wired in `bootstrap/app.php` (`then:`). Config in `config/nexo.php` (`nexo.beacon.*`).
- **Emitter**: `resources/js/nexo-beacon.js` (reads `nexo:beacon-*` metas, honours
  `navigator.doNotTrack`, `sendBeacon` on pageload). Wired here as the reference
  (`partials/beacon`, rendered only when the beacon is on); the other tools and alvarocdev copy it.
- **/admin**: aggregated metrics from `beacon_events`, gated by `sub` ∈ `NEXO_ADMIN_SUBS` (empty =
  no admin surface at all).

## Key decisions

- **2026-07-19** — Phase 0 executed with the `new-project` skill; ADRs 001–005. Alvaro's calls:
  subdomain `nexotools.alvarocdev.com`; the GitHub org is a phase of this project; the ecosystem
  switcher lives here as the canonical copyable template.
  **Superseded:** ADR-002 chose a static site with no backend for v1. The hub is a full Laravel app
  with MySQL — accounts, the springboard and the beacon receiver all need one. The ADR records the
  reasoning of the day, not the current architecture.
- **2026-07-19** — Dependency with nexoid registered on both sides:
  `docs/adr/ADR-003-nexoid-boundary.md` here and `ADR-006-nexotools-hub-client.md` in
  [nexo-id](https://github.com/nexo-tools/nexo-id).
  v1 does not depend on nexoid in any way.

## Accumulated context

- **2026-07-28** — **Ecosystem normalization run** (`alvaro/inbox/ecosystem-normalization/`). This
  file was the worst offender in the ecosystem: it was in Spanish claiming the repo was private,
  said production was "not deployed yet" with the site live, described the stack as a static site
  with no database, pointed at a `config/tools.php` that no longer exists, and called the
  app-switcher deferred when it has a guardian. All corrected here. Also landed: legal pages, the
  three new guardians, `composer audit` in CI, the neutral attribution default, and a README
  rewritten as the product's visual face with the operational detail moved to DEPLOYMENT.md.
- **2026-07-19** — The GitHub org `nexo-tools` exists; filling it and transferring the sibling
  repos was Phase 2 and is **done** — all six product repos live there. A dedicated domain was
  discarded for now (`nexo.tools` unavailable).
