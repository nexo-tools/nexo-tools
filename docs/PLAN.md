# PLAN — NexoTools

> La ejecución sigue la skill `planning-by-stages` (repo de estándares alvaro): una tarea numerada a la vez, checklist marcado al momento, SPEC antes del código, trazabilidad AC ↔ test por nombre, un commit por tarea (`"N,M descripción"`), CI verde antes de la siguiente, gate por fase con sign-off del owner.
>
> Por la regla just-in-time, solo la fase actual está desglosada en tareas. Las fases posteriores listan objetivo, trabajo clave y criterios de gate; sus tareas se derivan de los ACs de su SPEC al abrir la fase.

## Fase 0 — Planning y fundaciones (actual)

**Objetivo:** decisiones tomadas y registradas, alcance fijado, proyecto formalizado. Cero código de producto.

- [x] 0.1 Leer sistema de estándares + brief (nexotools.md) + plan de Fase 0 de nexoid; separar hechos de huecos.
- [x] 0.2 `docs/SCOPE.md` — propuesta de valor, MVP dentro/fuera, principios, backlog.
- [x] 0.3 ADRs fundacionales 001–005 (superficies/audiencias, stack v1, frontera nexoid, switcher de ecosistema, alcance org GitHub), estado Propuesto; ADR-006 propuesto en el lado de nexoid (registro bilateral).
- [x] 0.4 `docs/PLAN.md` (este archivo) con fases y gates.
- [x] 0.5 Formalización: `AGENTS.md` (ES), `CLAUDE.md` → AGENTS + briefing de estándares (repo privado), `README.md` con línea Status, `.gitignore`, git init (sin commits — quedan para después del sign-off).
- [ ] 0.6 Presentar plan + decisiones a Alvaro; resolver abiertas; estampar sign-off.

**Gate 0 (requiere sign-off del owner):**
- [ ] ADRs 001–005 revisados y aceptados/enmendados; ADR-006 de nexoid aceptado en su repo.
- [ ] Decisiones ya tomadas en consulta (2026-07-19) confirmadas: subdominio `nexotools.alvarocdev.com`; org GitHub como Fase 2 de este proyecto; stack v1 estático patrón alvarocdev; switcher como plantilla copiable canónica aquí.
- [ ] Abiertas a resolver: nombre del repo en la org (propuesta: `nexo-tools/nexotools`); compra defensiva de dominio (backlog, sin apuro); autorización de commit inicial + creación del repo privado en la org.
- [ ] SCOPE MVP dentro/fuera aprobado.
- [ ] Sign-off: _pendiente_.

## Fase 1 — v1: escaparate estático en producción

**Objetivo:** `nexotools.alvarocdev.com` live con las 4 tools (nexolinks y nexoagenda activas; nexoshort y nexoevents "próximamente"), construido SPEC-first.

Trabajo clave: `SPEC.md` con ACs numerados (contenido por tool: frase humana + 2-3 pasos con screenshots + botón directo + estado; sección "¿Eres developer?"; SEO base completo — titles/descriptions únicos, OG absolutos, canonical, JSON-LD, sitemap, hreflang; i18n es/en/pt con generador y test guardián; footer powered-by con UTM; accesibilidad básica). **Tarea 1.1 es el spike**: reutilizar el molde estático de alvarocdev (build, i18n de archivo único, tokens de diseño, CI+deploy FTP) y reconciliar SPEC + ADR-002 con lo hallado. Screenshots reales de nexolinks/nexoagenda. CI con lint + tests + `npm audit`. Deploy por Actions+FTP (regla no-clean-slate) y checklist `validate-generated-site`. Alta del subdominio en Hostinger. Monitoreo de uptime.

**Gate 1:** todos los ACs verdes con tests trazados por nombre (pasada `grep`); checklist `validate-generated-site` completa; verificación end-to-end real en producción (HTTP + navegación en los 3 idiomas); links a tools verificados; uptime configurado; sign-off del owner.

## Fase 2 — Cara técnica: contenido de la org `nexo-tools`

**Objetivo:** la org de GitHub como superficie developer del ecosistema (ADR-005).

Trabajo clave: repo `.github` con `profile/README.md` en inglés (mapa del ecosistema, relaciones entre repos, stack, self-hosting, contribución); identidad de la org (avatar, descripción, URL al hub); transfers de nexolinks y nexoagenda ejecutados con Alvaro repo por repo, con verificación post-transfer de Actions/secrets/deploys; pinned repos; revisión de READMEs por repo (huecos → deuda registrada en cada repo).

**Gate 2:** org completa y navegable; redirects de URLs viejas verificados; CI/deploy de cada tool migrada verde post-transfer; linking según ADR-001; sign-off del owner.

## Fase 3 — v2: hub integrado (bloqueada por nexoid)

**Objetivo:** el hub como cara visible de Nexo ID: login, "tus tools" con acceso con sesión iniciada, descubrimiento ("también puedes usar…").

**Precondición dura (ADR-003):** nexoid Fase 2 completada (provider en producción) y patrón de cliente de su Fase 3 disponible. Al abrir la fase: re-evaluar stack (la v1 estática no soporta cliente OIDC — posible re-plataforma, prevista en ADR-002), resolver el deslinde "your tools" con nexoid (ADR-003 §4), SPEC y ADRs nuevos si el contrato estándar no alcanza.

**Gate 3:** flujo real login → "tus tools" → salto a una tool con sesión iniciada; degradación verificada (nexoid caído → contenido estático intacto); sign-off del owner.

## Fase 4 — Integración inversa: componente de ecosistema en cada tool

**Objetivo:** desde cualquier tool se descubre y salta al resto (ADR-004).

Trabajo clave: SPEC del componente (plantilla copiable + contrato de datos, variantes HTML/Blade/TS); implantación coordinada con el plan de cada tool (nexolinks, nexoagenda, nexoshort, nexoevents cuando exista); propuesta de registro en CATALOG.md del repo alvaro.

**Gate 4:** componente replicado en las tools activas, verificado en producción de cada una; pieza registrada en CATALOG (aprobada por Alvaro); sign-off del owner.
