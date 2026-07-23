# ADR-002 — Stack y hosting de la v1: sitio estático patrón alvarocdev (Actions + FTP al shared host)

- **Fecha:** 2026-07-19
- **Estado:** Propuesto (pendiente de gate 0)

## Contexto

La v1 es contenido casi estático: tools, descripciones, pasos con screenshots, links, estados. Sin login, sin backend, sin datos de usuario. La elección de stack debe justificarse contra esa realidad (sin sobre-ingeniería) y contra la dirección de stack del ecosistema (estratégica: TS end-to-end; pragmática: lo que despliega hoy en el shared host de Hostinger, pagado por 3 años).

## Decisión

Decisión de Alvaro (2026-07-19, consulta de Fase 0): **sitio estático con el patrón de alvarocdev.com**, reutilizando sus piezas canónicas (CATALOG.md):

1. **Build estático** con i18n de archivo único (`translations.ts` tipado + páginas finas por idioma con prop `lang`) — es/en/pt desde el primer commit, con generador y test guardián.
2. **Deploy**: GitHub Actions buildea y sube `dist/` por FTP con cuenta chrooteada al subdominio `nexotools.alvarocdev.com` del shared host. Regla no-clean-slate. Costo $0.
3. **Tokens de diseño**: base en las variables CSS de alvarocdev, adaptadas a la identidad Nexo (detalle en el SPEC de Fase 1).
4. **Modelo de datos y API del MVP: no hay.** El "modelo" es el contenido versionado en el repo (la lista de tools con nombre, URL, estado y textos). Cualquier estructura interna (p. ej. un archivo de datos de tools) se define en el SPEC de Fase 1.
5. **Anti-abuso: n/a en v1** — no hay inputs públicos (sin formularios). Si algún día se añade uno (contacto, waitlist), aplica el estándar: rate limiting + reto self-hosted sin cookies; nunca reCAPTCHA/terceros.
6. **Analytics**: cookieless únicamente. Se adopta el beacon propio de alvarocdev (CATALOG) cuando esté disponible; mientras tanto, sin analytics de producto. Nada de Google Analytics.
7. **Operación**: monitoreo de uptime del subdominio; backups n/a (sitio estático — el repo es la fuente; el deploy es reproducible).
8. **Visibilidad del repo**: nace **privado** en la org `nexo-tools` (default de la skill new-project); publicarlo es decisión posterior de Alvaro + `audit-open-source`. Docs en español mientras sea privado; si se publica, la migración de docs a inglés queda como deuda registrada a resolver en esa decisión.

## Alternativas consideradas

- **Next.js estático en Vercel** — descartado: alineado a la dirección TS y camino más suave a la v2, pero es maquinaria de más para puro contenido hoy. La v2 (login OIDC) se decidirá cuando toque; si exige re-plataforma, la migración es un redirect + reaprovechar contenido.
- **Astro en Vercel** — descartado: óptimo para estáticos multi-idioma, pero stack nuevo en el ecosistema; pieza no reutilizable y sin referencia canónica propia.
- **Laravel en el shared host** — descartado: un framework con backend para una página sin backend es la definición de sobre-ingeniería.

## Consecuencias

- Reutiliza tres piezas canónicas ya probadas (i18n archivo único, CI+deploy FTP, tokens de diseño) — mínimo código nuevo, máxima consistencia.
- Riesgo asumido y aceptado: la v2 integrada probablemente exija re-plataforma (cliente OIDC necesita server o edge). Se decide en su fase, con la frontera del ADR-003 protegiendo que nada de la v1 dependa de ello.
- El spike de Fase 1 (tarea 1.1) valida que el molde de alvarocdev se reutiliza limpio en un segundo proyecto y reconcilia el SPEC con lo hallado.
