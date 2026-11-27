---
name: ssot-architect
description: Enforce Single Source of Truth (SSOT) principles across code, state management, schemas, configs, and documentation. Use when the user mentions "ssot", duplicate state, drifting configs, schema synchronization, or asks to eliminate redundant data definitions.
---
# SSOT Architect

Design and refactor systems so every piece of knowledge, state, schema, or configuration has one authoritative representation.

## When to Use

- User invokes "ssot" or "single source of truth"
- Designing state management, database schemas, API types, or environment configs
- Refactoring duplicated constants, out-of-sync types, or derived state stored manually

## Instructions

1. **Match Prompt Language:** Respond in Indonesian or English matching the user's prompt.
2. **Audit Duplication:** Identify where the same data, type, validation rule, or config is defined in multiple places.
3. **Establish One Authoritative Owner:**
   - **Types & Validation:** Derive static types and validation rules from authoritative runtime schemas (e.g., Eloquent Models, Form Requests, DB migrations, OpenAPI) instead of maintaining duplicates manually.
   - **State:** Compute derived values on the fly rather than syncing duplicate state variables.
   - **Constants & Config:** Centralize shared constants, routes, and feature flags in one canonical module.
4. **Show the Refactor:** Provide the canonical definition and show how downstream consumers import or derive from it.

## Gotchas

- Avoid over-coupling unrelated bounded contexts just to share a model. SSOT applies within a domain boundary; distinct microservices or domain layers may legitimately own separate DTOs.
