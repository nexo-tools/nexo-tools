<div align="center">

<img src="resources/brand/isotype.svg" width="88" alt="Nexo Tools isotype">

# Nexo Tools

**One home for the whole Nexo ecosystem — discover every tool, hop between them with one account, and watch the family from a single cookieless dashboard.**
Self-hosted, no lock-in, no trackers.

[![CI](https://github.com/nexo-tools/nexo-tools/actions/workflows/ci.yml/badge.svg)](https://github.com/nexo-tools/nexo-tools/actions/workflows/ci.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-7C3AED.svg)](LICENSE)
![PHP 8.3+](https://img.shields.io/badge/PHP-8.3%2B-777bb4.svg)
![Laravel 13](https://img.shields.io/badge/Laravel-13-ff2d20.svg)

[Deployment guide](DEPLOYMENT.md) ·
[Scope](docs/SCOPE.md) ·
[Plan & gates](docs/PLAN.md)

</div>

---

Nexo Tools is the **hub of the Nexo family**: a self-hosted homepage that presents
every tool, explains what each one does, and gets people into them in one click. It
also carries the ecosystem's shared identity — the app-switcher woven into every
sibling tool, an optional shared account ([Nexo ID](https://github.com/nexo-tools/nexo-id)
SSO) with a personal *"your tools"* springboard, and a cookieless dashboard that
watches the whole family. It doubles as the **reference implementation** of the Nexo
brand and chrome that every other tool mirrors.

## Why Nexo Tools?

- **One front door** — the landing page lists every tool with a one-line pitch and a
  direct link, so a non-technical visitor can find and open the right one without
  reading a repo.
- **Move between tools anywhere** — the shared app-switcher (part of the Nexo chrome
  every sibling ships) lets people jump from one tool to another from any page.
- **One optional account** — sign in with Nexo ID and get a personal *"your tools"*
  springboard: mark the tools you use, launch them, and curate your own shortcuts.
  Turn SSO off and the hub runs perfectly standalone — the account layer simply
  disappears.
- **Cookieless ecosystem analytics** — a tiny beacon records visits with **no cookies,
  no raw IPs, and no cross-site tracking**; it honours Do Not Track and Global Privacy
  Control, and visitor hashes rotate daily. No consent banner needed.
- **One dashboard for the whole family** — `/admin` aggregates the beacon into total
  visits, daily unique visitors and a per-tool breakdown — reading only your own
  database, never an external service.
- **Admin gated by identity** — the dashboard is locked to an allowlist of Nexo ID
  accounts; with no allowlist configured there is **no admin surface at all**.
- **The brand reference** — canonical Nexo look and chrome (violet tokens, automatic
  light/dark, shared footer, translatable `/help` center) that the sibling tools copy
  to stay visually consistent.
- **Multilingual** — English, Spanish and Portuguese (`en`/`es`/`pt`) with a visible
  switcher.
- **Fast, private, portable** — server-rendered, system fonts, **zero external
  requests** (no CDNs, no font services, no trackers), and self-hostable on your own
  domain.

## Tech stack

PHP 8.3+ · Laravel 13 · Blade + Alpine.js + Tailwind CSS (Vite) · MySQL

Quality: [Pest](https://pestphp.com) · [Pint](https://laravel.com/docs/pint) ·
[Larastan](https://github.com/larastan/larastan) · GitHub Actions CI.
Zero external runtime requests — no CDNs, no Google Fonts, system font stack.

## Quick start (local)

Requirements: Docker — everything else runs in containers via
[Laravel Sail](https://laravel.com/docs/sail).

```bash
git clone https://github.com/nexo-tools/nexo-tools.git
cd nexo-tools
cp .env.example .env
docker run --rm -v "$(pwd):/app" -w /app composer:latest composer install
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
./vendor/bin/sail npm install && ./vendor/bin/sail npm run build
```

Open [http://localhost](http://localhost). Local email inbox (Mailpit):
[http://localhost:8025](http://localhost:8025).

## Self-hosting

Nexo Tools is a standard Laravel app — run the whole hub on your own domain. See the
step-by-step guide: **[DEPLOYMENT.md](DEPLOYMENT.md)**.

### Configuration

| Env var | Purpose | Default |
| --- | --- | --- |
| `NEXO_ATTRIBUTION_LABEL` | "Powered by" label in the shared footer | unset |
| `NEXO_ATTRIBUTION_URL` | Footer link target | unset |
| `NEXO_SUPPORT_EMAIL` | Contact address on the `/help` center | `hola@alvarocdev.com` |
| `NEXO_SUPPORT_URL` | Support URL (wins over the mailto when set) | unset |
| `NEXO_SSO_ENABLED` | Enable Nexo ID SSO to unlock accounts + *your tools* | `false` |
| `NEXO_ADMIN_SUBS` | CSV of Nexo ID `sub`s allowed into `/admin` (empty = no admin) | empty |
| `NEXO_BEACON_ENABLED` | Turn on cookieless ecosystem analytics | `false` |
| `NEXO_BEACON_ENDPOINT` | Where the browser snippet posts | `/beacon` |
| `NEXO_BEACON_RATE_LIMIT` | Beacon requests per minute per IP | `60` |

The tool registry that powers the landing, the app-switcher and the beacon origin
allowlist lives in [`config/nexo-ecosystem.php`](config/nexo-ecosystem.php); the rest
in [`config/nexo.php`](config/nexo.php).

## Status

v1 (public ecosystem hub) is **built**, and so is the v2 account layer — the *"your
tools"* springboard, the `/admin` metrics dashboard and the cookieless beacon — on top
of the shared Nexo brand/chrome, `/help` and i18n (`es`/`en`/`pt`). This repo is the
**living reference** of the Nexo brand standard the other tools copy. Not yet deployed:
production hardening and launching `nexotools.alvarocdev.com` are the remaining work.

## Documentation

- [Scope](docs/SCOPE.md)
- [Plan & gates](docs/PLAN.md)
- [Decisions (ADRs)](docs/adr/)
- [Deployment guide](DEPLOYMENT.md)

## Nexo ecosystem

Nexo is a family of open-source, self-hostable tools that share one visual identity
([nexo-brand](https://github.com/nexo-tools)), one optional account
([Nexo ID](https://github.com/nexo-tools/nexo-id) SSO) and one set of engineering
standards. Every tool runs **fully standalone** — the ecosystem is opt-in.

| Tool | What it is | Repo |
| --- | --- | --- |
| **Nexo Tools** | Ecosystem hub — discover the tools and hop between them with one account | — you are here |
| **Nexo Links** | Link-in-bio you host yourself (Linktree alternative) | [nexo-links](https://github.com/nexo-tools/nexo-links) |
| **Nexo Agenda** | Bookings for service businesses (AgendaPro / Fresha / Booksy alternative) | [nexo-agenda](https://github.com/nexo-tools/nexo-agenda) |
| **Nexo Short** | Self-hosted URL shortener | [nexo-short](https://github.com/nexo-tools/nexo-short) |
| **Nexo Events** | Event tickets and passes | [nexo-events](https://github.com/nexo-tools/nexo-events) |
| **Nexo ID** | One account for every tool — OAuth 2.0 / OIDC SSO | [nexo-id](https://github.com/nexo-tools/nexo-id) |

New to Nexo? Start at **[nexotools.alvarocdev.com](https://nexotools.alvarocdev.com)**.
Built by **[alvarocdev.com](https://alvarocdev.com)** — the tech behind Nexo.

## License

[MIT](LICENSE) © [Alvaro Carrizales](https://alvarocdev.com) — the tech behind Nexo.
