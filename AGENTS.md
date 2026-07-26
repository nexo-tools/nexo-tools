# NexoTools

> Punto de entrada para cualquier IA/agente que trabaje en este proyecto. Sigue el sistema de estándares de Alvaro (repo `alvaro`, alvarocdev.com). Mantén este archivo actualizado: persiste aquí el contexto importante que surja en las sesiones de trabajo.
> Si el repo es público, este archivo también lo es: nada de secretos, credenciales ni infra sensible aquí.

## Qué es este proyecto

Portada/hub del ecosistema Nexo para usuarios finales **no técnicos**: qué hace cada tool, cómo se usa y acceso directo. La cara técnica del ecosistema (org de GitHub `nexo-tools`) también es alcance de este proyecto (Fase 2). Estado: **v1 hub construido (2026-07-23)** — Laravel (clonado de la infra de nexoagenda/nexoevents: Sail sobre dev-environment compartido, Pest/Pint/Larastan L6, CSP + sync test, i18n es/en/pt + guardián, template `nexo-sso-client` off). El hub público lee `config/tools.php` (grid de tools con nombre/tagline/estado/link, badges activa/próximamente, link a la org GitHub, footer attribution). Andamiaje v2 presente (cuentas + SSO por env → "tus herramientas" en `DashboardController`). 48 tests verde (Pint+Larastan+audit+i18n). **Deferido:** "tus tools" real por uso; **app-switcher** del ecosistema replicado en cada tool; deploy (owner-gated). Brief original: `nexotools.md` (Claude Cowork, 20/07/2026). Detalle de la corrida: `~/alvaro/inbox/ecosystem-audit/`.

## Stack

v1: sitio estático patrón alvarocdev — i18n de archivo único (es/en/pt), build a `dist/`, deploy GitHub Actions + FTP al shared host de Hostinger, subdominio `nexotools.alvarocdev.com`. Sin backend, sin base de datos (ADR-002). Detalle fino se fija en el spike 1.1.

## Cómo correrlo

Sin PHP/Composer local — todo por Docker/Sail. Desde 2026-07-26 los servicios
con estado (MySQL, Mailpit, phpMyAdmin) vienen del entorno compartido
(`~/dev-environment`, proyecto compose `nexo`): MySQL en puerto host **3307**
(DB `nexo_tools`, usuario/clave `dev`/`dev`), Mailpit SMTP 1025 / UI 8025,
phpMyAdmin 8306. El `compose.yaml` de este repo solo corre el runtime de la
app (`APP_PORT=8080` / `VITE_PORT=5173` / `WWWUSER`/`WWWGROUP` fijados en `.env`).

```bash
cd ~/dev-environment && docker compose up -d mysql mailpit  # servicios compartidos
docker run --rm -v "$PWD":/app -w /app composer:latest install
docker compose up -d                                # app en http://localhost:8080
docker compose exec laravel.test php artisan migrate
npm install && npm run build
# checks (como CI)
docker run --rm -v "$PWD":/app -w /app composer:latest sh -c 'vendor/bin/pint --test && vendor/bin/phpstan analyse && vendor/bin/pest'
```

## Producción

Aún no deployado. Destino: `nexotools.alvarocdev.com` (Hostinger shared, FTP chrooteado, regla no-clean-slate).

## Convenciones del proyecto

- El proyecto se ejecuta con la skill `planning-by-stages`: ver `docs/PLAN.md` (rector), `docs/adr/` (decisiones), `docs/SCOPE.md`. Una tarea a la vez; commits `"N,M descripción"`.
- Repo **privado** por ahora → docs en español; si se publica (decisión de Alvaro + `audit-open-source`), migrar docs a inglés (deuda registrada en ADR-002).
- Cero jerga técnica en el contenido del hub; la única mención técnica es la sección "¿Eres developer?" → org de GitHub.
- El hub no duplica las landings de las tools (anti-canibalización SEO, ADR-001).

## Beacon (analítica cookieless del ecosistema, opt-in)

El hub ingiere pageviews de las tools + alvarocdev vía `POST /beacon` (v2, M5). **Off por defecto** (`NEXO_BEACON_ENABLED=false`): sin él, `/beacon` responde 204 y no escribe — la app v1 sigue intacta.

- **Privacidad**: cookieless, sin IP/UA/PII persistidos. Solo un `visitor_hash` anónimo diario (SHA-256 de app key + fecha + IP + UA — nada crudo, patrón canónico `VisitorHash` de nexolinks), `origin` (slug de tool), `path` truncado a 255, `day`, `country` (ISO-2 desde header de edge si existe) y `ref` (slug de tool que refirió, para atribución cross-tool en la vista alvarocdev). Respeta `DNT`/`Sec-GPC`. Nunca setea cookie.
- **Allowlist**: solo acepta `origin`/`ref` ∈ claves de `config('nexo.beacon.origins')` (deriva de `nexo-ecosystem` + alvarocdev). CORS emite `Access-Control-Allow-Origin` solo a esos hosts; maneja preflight OPTIONS. Rate limit por IP (`NEXO_BEACON_RATE_LIMIT`, 60/min).
- **Ruta fuera del grupo web** (sin sesión → sin `Set-Cookie`, sin CSRF): ver `routes/beacon.php` cableado en `bootstrap/app.php` (`then:`). Config en `config/nexo.php` (`nexo.beacon.*`).
- **Emisor**: snippet `resources/js/nexo-beacon.js` (lee metas `nexo:beacon-*`, respeta `navigator.doNotTrack`, `sendBeacon` en pageload). Cableado en nexotools como referencia (`partials/beacon`, solo se renderiza con el beacon activo). Las demás tools/alvarocdev lo copian — ver `resources/js/nexo-beacon.js` (documentado como asset compartible de `nexo-ui`).
- **/admin**: métricas agregadas de `beacon_events`, gated por `sub` ∈ `NEXO_ADMIN_SUBS` (vacío = sin superficie admin). Cookieless, sin requests externos.

## Decisiones importantes

- **2026-07-19** — Fase 0 ejecutada con la skill `new-project`; ADRs 001–005 propuestos. Decisiones de Alvaro en consulta: subdominio `nexotools.alvarocdev.com`; org GitHub como fase de este proyecto; stack v1 estático patrón alvarocdev; switcher de ecosistema como plantilla copiable canónica en este repo. Ver `docs/adr/`.
- **2026-07-19** — Dependencia con nexoid registrada en ambos lados: `docs/adr/ADR-003-nexoid-boundary.md` aquí y `ADR-006-nexotools-hub-client.md` en `~/nexoid` (propuesto, pendiente de sign-off allá). La v1 no depende de nexoid bajo ningún concepto.

## Contexto acumulado

- **2026-07-19** — La org de GitHub `nexo-tools` (display "NexoTools") ya existe pero está vacía de contenido; su llenado y los transfers de nexolinks/nexoagenda son la Fase 2. Dominio propio descartado por ahora (nexo.tools no disponible); compra defensiva en backlog. El plan de nexoid vive en `~/nexoid` (Fase 0 firmada 2026-07-19).
