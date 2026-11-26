---
name: edge-case-hunter
description: |
  Audit code, APIs, or designs for boundary failures, concurrency bugs, null/malformed inputs, and external timeouts. Use when the user asks for "edge cases", "failure check", "break my code", or wants a pre-merge reliability audit (for security/OWASP audits, use owasp-security-auditor).
  
---
# Edge-Case Hunter

Systematically hunt down boundary failures, race conditions, and reliability bottlenecks before code hits production.

## When to Use

- User says "edge cases", "find bugs", "break this code", or asks for a pre-PR/pre-release reliability check
- Reviewing payment flows, async workers, file parsers, state transitions, or database writes
- Hardening functions against unexpected real-world inputs and network failures

## Instructions

1. **Match Prompt Language:** Respond in Indonesian or English matching the user's prompt.
2. **Scan 4 Reliability Failure Vectors:**
   - **Null / Empty / Malformed Inputs:** Missing fields, zero/negative values, NaN/Infinity, huge payloads, invalid encodings.
   - **Boundaries & Off-by-One:** Empty arrays, single-element lists, integer overflow, timezone/DST rollover, pagination limits.
   - **Concurrency & State Races:** Duplicate submissions, double-clicks, stale reads, deadlocks, out-of-order async callbacks.
   - **External & Network Failures:** Timeouts, partial writes, retry storms, non-idempotent side effects.
3. **Report Only Real Risks:**
   - List only failure modes that actually apply to the provided code/design, ordered by severity.
   - For each issue, state: **Failure Scenario** -> **Impact** -> **Minimal Fix**.

## Gotchas

- Leave security vulnerabilities (injection, auth, IDOR, XSS) to `owasp-security-auditor` so this audit stays laser-focused on runtime reliability and logic edge cases.
- Do not invent theoretical enterprise risks for a simple local script. Calibrate severity to the actual execution context.
