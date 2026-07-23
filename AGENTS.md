# NexoTools

> Punto de entrada para cualquier IA/agente que trabaje en este proyecto. Sigue el sistema de estándares de Alvaro (repo `alvaro`, alvarocdev.com). Mantén este archivo actualizado: persiste aquí el contexto importante que surja en las sesiones de trabajo.
> Si el repo es público, este archivo también lo es: nada de secretos, credenciales ni infra sensible aquí.

## Qué es este proyecto

Portada/hub del ecosistema Nexo para usuarios finales **no técnicos**: qué hace cada tool, cómo se usa y acceso directo. La cara técnica del ecosistema (org de GitHub `nexo-tools`) también es alcance de este proyecto (Fase 2). Estado: **Fase 0 (planning), gate pendiente de sign-off**. Brief original: `nexotools.md` (Claude Cowork, 20/07/2026).

## Stack

v1: sitio estático patrón alvarocdev — i18n de archivo único (es/en/pt), build a `dist/`, deploy GitHub Actions + FTP al shared host de Hostinger, subdominio `nexotools.alvarocdev.com`. Sin backend, sin base de datos (ADR-002). Detalle fino se fija en el spike 1.1.

## Cómo correrlo

Aún no hay código (Fase 0). Los comandos se documentan aquí al cerrar el spike 1.1.

## Producción

Aún no deployado. Destino: `nexotools.alvarocdev.com` (Hostinger shared, FTP chrooteado, regla no-clean-slate).

## Convenciones del proyecto

- El proyecto se ejecuta con la skill `planning-by-stages`: ver `docs/PLAN.md` (rector), `docs/adr/` (decisiones), `docs/SCOPE.md`. Una tarea a la vez; commits `"N,M descripción"`.
- Repo **privado** por ahora → docs en español; si se publica (decisión de Alvaro + `audit-open-source`), migrar docs a inglés (deuda registrada en ADR-002).
- Cero jerga técnica en el contenido del hub; la única mención técnica es la sección "¿Eres developer?" → org de GitHub.
- El hub no duplica las landings de las tools (anti-canibalización SEO, ADR-001).

## Decisiones importantes

- **2026-07-19** — Fase 0 ejecutada con la skill `new-project`; ADRs 001–005 propuestos. Decisiones de Alvaro en consulta: subdominio `nexotools.alvarocdev.com`; org GitHub como fase de este proyecto; stack v1 estático patrón alvarocdev; switcher de ecosistema como plantilla copiable canónica en este repo. Ver `docs/adr/`.
- **2026-07-19** — Dependencia con nexoid registrada en ambos lados: `docs/adr/ADR-003-nexoid-boundary.md` aquí y `ADR-006-nexotools-hub-client.md` en `~/nexoid` (propuesto, pendiente de sign-off allá). La v1 no depende de nexoid bajo ningún concepto.

## Contexto acumulado

- **2026-07-19** — La org de GitHub `nexo-tools` (display "NexoTools") ya existe pero está vacía de contenido; su llenado y los transfers de nexolinks/nexoagenda son la Fase 2. Dominio propio descartado por ahora (nexo.tools no disponible); compra defensiva en backlog. El plan de nexoid vive en `~/nexoid` (Fase 0 firmada 2026-07-19).
