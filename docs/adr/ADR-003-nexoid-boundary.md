# ADR-003 — Frontera con nexoid: v1 sin dependencia alguna; v2 como cliente OIDC estándar

- **Fecha:** 2026-07-19
- **Estado:** Propuesto (pendiente de gate 0)

## Contexto

Nexo ID (`~/nexoid`, Fase 0 con sign-off del 2026-07-19) es el servicio central de identidad del ecosistema: OAuth 2.0 authorization code + PKCE con capa de identidad OIDC (su ADR-003), integración opcional por env en cada tool (su ADR-004). El brief de NexoTools define dos fases: v1 escaparate estático y v2 hub integrado, donde NexoTools se convierte en la cara visible de nexoid. Su plan (Fase 5) incluye además una página de cuenta "your tools" — hay riesgo de solape. La dependencia debe registrarse en ADRs de ambos lados con la frontera v1/v2 explícita.

## Decisión

1. **La v1 no depende de nexoid bajo ningún concepto** (decisión de Alvaro, no re-evaluable): sin login, sin sesión, sin llamadas a nexoid, sin esperar a que exista. Todo lo de la v1 (contenido, links directos, estados, sección developer) funciona con nexoid apagado o inexistente.
2. **La v2 será un cliente OIDC estándar de nexoid**, exactamente como cualquier tool (nexoid ADR-003/004): login con Nexo ID, "tus tools" con acceso directo con sesión iniciada, descubrimiento ("también puedes usar…"). Nada bespoke: el hub consume el mismo contrato que Nexo Short estrenará en la Fase 3 de nexoid.
3. **Precondición de la v2**: nexoid productivo como provider (su Fase 2 completada, patrón de cliente de su Fase 3 disponible). La v2 de este plan queda explícitamente bloqueada por eso y no antes.
4. **Deslinde con la página "your tools" de nexoid (su Fase 5)**: nexoid es gestión de **cuenta** (perfil, sesiones, seguridad, conexiones); el hub es **descubrimiento y acceso** (qué tools existen, cuáles usas, saltar a ellas). Si al abrir la v2 el solape persiste, se resuelve en ese momento entre ambos proyectos — ninguno implementa la parte del otro por su cuenta.
5. **Registro bilateral**: este ADR en NexoTools + `ADR-006-nexotools-hub-client.md` propuesto en nexoid (pendiente de sign-off de Alvaro allá, dado que su gate 0 ya cerró).

## Alternativas consideradas

- **Acoplar la v1 a nexoid (esperar al login para lanzar)** — rechazado por decisión explícita de Alvaro: mata el objetivo de que el ecosistema *se vea* como ecosistema ya.
- **Integración bespoke hub↔nexoid (endpoint especial "tools del usuario")** — rechazado a priori: el hub debe ser un cliente estándar; si la v2 necesita datos que el contrato estándar no da, se decide en el planning de la v2 con ADR en ambos lados.

## Consecuencias

- NexoTools v1 puede lanzarse de inmediato, en paralelo al desarrollo de nexoid.
- La v2 hereda gratis el patrón de cliente que nexoid construye en su Fase 3 (incluida degradación: nexoid caído no puede tumbar el contenido estático del hub).
- El planning de la v2 (fase futura de este plan) se hace contra el estado real de nexoid en ese momento, no contra supuestos de hoy.
