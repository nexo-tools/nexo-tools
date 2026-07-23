# SCOPE — NexoTools

<!-- Registro vivo: toda idea nueva se asienta aquí (commit docs:) ANTES de implementarse. -->

## Propuesta de valor

NexoTools es la portada del ecosistema Nexo: un lugar donde cualquier persona **sin conocimiento técnico** ve todas las herramientas, entiende qué hace cada una y cómo funciona, y entra directo a usarlas. Cierra el círculo del ecosistema: desde cualquier tool se descubre y salta a las demás, con la cuenta compartida (Nexo ID) como pegamento en su fase integrada (v2).

La cara técnica (repos, open source, arquitectura) NO vive aquí: vive en la organización de GitHub `nexo-tools` (ver ADR-001 y ADR-005).

## MVP (v1 — escaparate estático)

### Dentro

- Página estática en `nexotools.alvarocdev.com` con las tools del ecosistema:
  - **nexolinks** (Activa), **nexoagenda** (Activa), **nexoshort** (Próximamente), **nexoevents** (Próximamente).
  - Por tool: nombre + qué hace en una frase de humano; cómo funciona en 2-3 pasos simples con screenshots; botón directo a usarla; badge de estado (Activa / Beta / Próximamente).
- Sección separada y discreta al final: "¿Eres developer?" → link a la org de GitHub (única mención técnica del hub).
- Baseline de toda superficie pública del sistema: SEO base, i18n es/en/pt desde el primer commit, footer "powered by alvarocdev.com", analytics cookieless.

### Fuera (con el porqué)

- **Login / cualquier dependencia de nexoid** — la v1 no se acopla a nexoid bajo ningún concepto (decisión de Alvaro; frontera en ADR-003). Es la v2.
- **"Tus tools" y descubrimiento personalizado** — requieren sesión Nexo ID; v2.
- **Componente de ecosistema en cada tool (integración inversa)** — parte de v2; el ADR-004 fija dónde vivirá.
- **Plataforma para que terceros publiquen tools** — implicaría estándares de integración y revisión; visión lejana, fuera de alcance.
- **Documentación técnica** — eso es la org de GitHub.
- **Backend / modelo de datos / API** — la v1 es puro contenido; no hay (ADR-002).

## Principios del producto

- **Audiencias separadas**: hub = usuarios finales, org de GitHub = developers. Ninguna superficie mezcla ambas.
- **Lenguaje humano**: cero jerga técnica en el hub; se explica con frases de persona, pasos y capturas.
- **La v1 no espera a nadie**: estática a propósito, publicable ya, sin dependencia de nexoid.
- **"Próximamente" es contenido**: listar nexoshort y nexoevents genera expectativa gratis.
- **Sin sobre-ingeniería**: el stack se justifica contra la realidad del contenido, no contra la ambición de la v2 (ADR-002).

## Backlog post-v1

- **v2 — hub integrado** (cuando nexoid esté productivo): login con Nexo ID, "tus tools" con acceso directo con sesión iniciada, descubrimiento ("también puedes usar…"). Pospuesto: depende de nexoid Fase 2+ (ADR-003).
- **Integración inversa**: componente de ecosistema (switcher/footer) replicado en cada tool. Pospuesto: solo tiene sentido con la v2 y se coordina con el plan de cada tool (ADR-004).
- **Compra defensiva de dominio propio** (~$10/año, sin usarlo): dominio propio descartado por ahora (nexo.tools no disponible; todo bajo alvarocdev.com); la compra defensiva de una alternativa queda como decisión abierta de Alvaro.
- **Beacon de analytics cookieless propio**: si la pieza de alvarocdev (CATALOG) aún no está lista al salir la v1, el hub sale sin analytics de producto antes que con terceros.
