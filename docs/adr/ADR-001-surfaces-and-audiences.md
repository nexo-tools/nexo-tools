# ADR-001 — Superficies y audiencias: hub, org de GitHub y alvarocdev.com

- **Fecha:** 2026-07-19
- **Estado:** Propuesto (pendiente de gate 0)

## Contexto

El ecosistema Nexo tiene tres superficies públicas con riesgo de pisarse entre sí: el hub NexoTools, la organización de GitHub `nexo-tools` (ya creada, display "NexoTools") y alvarocdev.com (marca personal, objetivo actual del portafolio: todo apunta a ella). El dominio propio está descartado por ahora (nexo.tools no disponible; decisión de Alvaro: todo como parte de alvarocdev.com). Hay que fijar qué rol cumple cada superficie, qué linkea a qué y cómo evitar canibalización SEO.

## Decisión

1. **Reparto de audiencias** (del brief, no re-evaluable):

   | Superficie | Audiencia | Contenido | Rol SEO |
   |---|---|---|---|
   | Hub — `nexotools.alvarocdev.com` | Usuarios finales no técnicos | Qué hace cada tool, cómo se usa, acceso directo, estados | Rankear "herramientas Nexo" y descubrimiento del ecosistema como conjunto |
   | Org GitHub `nexo-tools` | Developers | Repos, arquitectura, self-hosting, contribución | Marca técnica/open source; no compite por keywords de producto |
   | alvarocdev.com | Clientes/red profesional de Alvaro | Marca personal, portafolio | Rankear el nombre y servicios de Alvaro |

2. **Subdominio del hub: `nexotools.alvarocdev.com`** — decisión de Alvaro (2026-07-19, consulta de Fase 0). Sigue el patrón de hermanos (`nexolinks.`, `nexoagenda.`, `nexoid.`) y coincide con el handle de la org.
3. **Quién linkea a quién:**
   - alvarocdev.com → hub (sección de productos/portafolio); no linkea tool por tool — el hub es la puerta.
   - Hub → cada tool (botón directo) y → org de GitHub solo desde la sección discreta "¿Eres developer?".
   - Cada tool → hub (hoy vía footer/enlace simple; en v2 vía el componente de ecosistema, ADR-004).
   - Org de GitHub → hub (README de perfil: "¿solo quieres usarlas?") y → alvarocdev.com como autoría.
4. **Anti-canibalización:** cada tool rankea por sus propias keywords en su propio subdominio; el hub no duplica el contenido de las tools (una frase + pasos + captura, no landing completa por tool); titles/descriptions únicos por superficie. El hub agrega, no sustituye.

## Alternativas consideradas

- **`nexo.alvarocdev.com`** — descartado por Alvaro: más corto pero rompe el patrón nombre-completo de los hermanos y no coincide con la org.
- **Hub dentro de alvarocdev.com (ruta `/nexo`)** — descartado: mezcla audiencias (marca personal vs usuarios de producto) y complica la migración futura a dominio propio (subdominio → redirect limpio).
- **Un solo sitio para usuarios y devs** — descartado: contradice la separación de audiencias del brief.

## Consecuencias

- La migración futura a dominio propio (si "Nexo" gana identidad separada) es un redirect del subdominio; nada se bloquea hoy.
- El contenido por tool en el hub debe mantenerse deliberadamente corto para no competir con las landings de las tools.
- La org de GitHub necesita contenido propio (README de perfil) — alcance en ADR-005.
