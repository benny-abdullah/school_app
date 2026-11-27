---
name: js-pragmatic-engineer
description: Write clean, modern, idiomatic JavaScript (ES2022+) using native Web APIs (Web Audio, Fetch, Alpine.js helpers) and minimal dependencies. Use when writing frontend scripts, audio feedback synthesizers, Chart.js integrations, or client-side barcode buffer handlers.
---
# JavaScript Pragmatic Engineer

Write modern, readable, zero-bloat JavaScript that leverages built-in runtime APIs instead of legacy patterns or unnecessary packages.

## When to Use

- User asks for vanilla JavaScript, ES modules, browser scripts, or Node.js code
- Refactoring callback hell, legacy `var` code, or bloated utility imports
- Handling async flows, fetch requests, streams, or event-driven logic

## Instructions

1. **Match Prompt Language:** Respond in Indonesian or English matching the user's prompt.
2. **Modern Idiomatic ES2022+:**
   - Default to `const`, use `let` only when reassignment is required, and never use `var`.
   - Use `async/await` with explicit `try/catch` error handling and `Promise.all` / `Promise.allSettled` for concurrent operations.
   - Use optional chaining (`?.`), nullish coalescing (`??`), `structuredClone`, `Object.groupBy`, and `Array.prototype.at` where appropriate.
3. **Prefer Built-in Platform APIs:**
   - Use native `fetch`, `URL` / `URLSearchParams`, `Intl`, `crypto.randomUUID()`, and Node built-ins (`node:fs/promises`, `node:test`) instead of external libraries like Axios, Moment, or Lodash for standard tasks.
4. **Predictable Error & Boundary Handling:** Validate external inputs early and throw or return explicit `Error` objects with clear context.

## Gotchas

- Avoid mutating function arguments or shared state in place. Return new objects/arrays unless mutation is explicitly required for performance.
