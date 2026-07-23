# ADR-005 — La cara técnica (org de GitHub `nexo-tools`) es alcance de este proyecto

- **Fecha:** 2026-07-19
- **Estado:** Propuesto (pendiente de gate 0)

## Contexto

La org de GitHub ya existe (handle `nexo-tools`, display "NexoTools"); los repos del ecosistema vivirán ahí, incluido el de este proyecto. Falta su contenido: README de perfil de la org, pinned repos, migración de los repos existentes vía transfer. ¿Es alcance de este proyecto o proyecto aparte?

## Decisión

Decisión de Alvaro (2026-07-19, consulta de Fase 0): **fase propia de este proyecto** (Fase 2 del PLAN), con su gate. Razón: es la otra mitad de la misma decisión de audiencias (hub = usuarios, org = developers, ADR-001); el brief los ordena juntos.

Alcance de esa fase:

1. **README de perfil de la org** (repo especial `.github`, `profile/README.md`): presentación del ecosistema para devs, mapa de repos y cómo se relacionan, stack, self-hosting, cómo contribuir. En **inglés** (superficie pública para developers).
2. **Identidad de la org**: avatar, descripción, URL apuntando al hub.
3. **Migración de repos existentes** (nexolinks, nexoagenda; los demás nacen ya en la org) vía "Transfer ownership" — GitHub redirige las URLs viejas. Cada transfer es acción sobre la cuenta de Alvaro: se ejecuta con él, repo por repo, verificando redirects e integraciones (Actions, deploy keys, webhooks) después de cada uno.
4. **Pinned repos** con los proyectos principales.
5. **README por repo**: verificación de que cada repo migrado tiene README propio bien estructurado; huecos se registran como deuda del repo correspondiente, no se rehacen aquí.

Fuera: verificación de dominio de la org (opcional, cuando convenga), y cualquier contenido de marketing — la org es superficie técnica.

## Alternativas consideradas

- **Proyecto aparte** — descartado por Alvaro: duplicaría planning para una superficie que comparte brief, decisiones y calendario con el hub.
- **Hacerlo informalmente (sin fase ni gate)** — descartado: los transfers tocan repos en producción (CI/deploy); merecen checklist y verificación como cualquier fase.

## Consecuencias

- Este repo documenta y ejecuta la cara técnica del ecosistema además de la web del hub; su AGENTS.md lo refleja.
- Los transfers reordenan URLs de repos en producción: la fase incluye verificación post-transfer de Actions/secrets/deploys de cada tool migrada.
- El README de perfil linkea al hub y a alvarocdev.com según el mapa de linking del ADR-001.
