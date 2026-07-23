# ADR-004 — Componente de ecosistema (switcher/header compartido): plantilla copiable con referencia canónica en nexotools

- **Fecha:** 2026-07-19
- **Estado:** Propuesto (pendiente de gate 0)

## Contexto

La integración inversa del brief: desde cualquier tool se ve el ecosistema — un elemento común (menú/footer "parte del ecosistema Nexo" o mini-switcher de apps) que lleva al hub o directo a las otras tools. Se define una vez y se replica en cada tool. Impacta a todas las tools y a clientes de stack mixto: las Laravel en producción (Blade) y las futuras TS (starter-master). La pregunta: ¿dónde vive el componente — `packages/ui` de starter-master, nexotools, otro?

## Decisión

Decisión de Alvaro (2026-07-19, consulta de Fase 0): **plantilla copiable con referencia canónica en el repo nexotools**, siguiendo el principio del repo de estándares ("plantillas copiables, no paquetes; cada proyecto es autónomo"):

1. El componente vive en este repo (p. ej. `templates/ecosystem-switcher/`) con variantes por stack: HTML/CSS base, adaptación Blade y adaptación TS/React. Cada tool lo **copia y adapta**; anota en su AGENTS.md de dónde vino.
2. Junto al componente vive su **contrato de datos**: la lista de tools del ecosistema (nombre, frase, URL, estado). La forma exacta (archivo estático versionado vs endpoint) se define en el SPEC de su fase — con sesgo a lo estático: las tools cambian poco.
3. Al existir, se propone registrarlo en el **CATALOG.md** del repo alvaro como pieza con referencia canónica aquí (propuesta a Alvaro, no aplicación directa).
4. **El diseño e implementación NO son de la v1**: pertenecen a la fase de integración inversa (junto a la v2, cuando nexoid esté productivo — ver PLAN). Este ADR solo fija dónde vivirá y bajo qué modelo de distribución.

## Alternativas consideradas

- **`packages/ui` de starter-master** — descartado: solo sirve nativamente a las tools TS; las Laravel necesitarían copia adaptada igual → dos mecanismos de distribución para un componente. Además acopla productos en producción a un starter interno.
- **Repo propio del componente** — descartado: un repo para un snippet replicado es overhead; la referencia canónica en el hub (el proyecto cuyo dominio es precisamente "el ecosistema como conjunto") es el hogar natural.
- **Paquete npm/composer** — descartado: contradice el principio vigente de plantillas copiables; se re-evaluaría solo si el sistema entero migra a paquetes.

## Consecuencias

- Sin dependencia de build/paquete entre tools: cada una es autónoma, al costo conocido de que los cambios al componente se propagan por copia (aceptado por el principio del sistema; las tools son pocas).
- El hub queda como fuente de verdad de "qué tools existen y en qué estado" — el mismo contenido alimenta al hub y al switcher.
- starter-master podrá incluir la copia TS en su plantilla cuando el componente exista, sin ser su dueño.
