---
name: knowledge-graph-mapper
description: Structure complex domains, codebases, architectures, and concepts into explicit entity-relationship knowledge graphs. Use when the user asks for "knowledge graph", entity mapping, dependency graphs, system relationships, or structured concept maps.
---
# Knowledge Graph Mapper

Map complex codebases, system architectures, or domain concepts into explicit nodes (entities) and directed edges (relationships).

## When to Use

- User asks for a "knowledge graph", dependency map, or entity-relationship breakdown
- Onboarding onto an unfamiliar codebase or complex domain
- Analyzing coupled modules, data flows, or impact radius of a change

## Instructions

1. **Match Prompt Language:** Respond in Indonesian or English matching the user's prompt.
2. **Extract Core Entities (Nodes):** Identify key actors, services, modules, tables, or concepts with a brief 1-line role for each.
3. **Define Directed Relationships (Edges):** Express connections using explicit `Source --[relationship]--> Target` triples (e.g., `AuthService --[issues]--> JWT`, `OrderTable --[references]--> UserTable`).
4. **Provide Visual + Queryable Output:**
   - Include a compact **Mermaid diagram** (`graph TD` or `graph LR`) for quick visual scanning.
   - List critical **clusters, bottlenecks, or circular dependencies** spotted in the graph.

## Gotchas

- Keep graphs focused (8–20 key nodes). Dumping every minor helper function turns a knowledge graph into unreadable noise.
