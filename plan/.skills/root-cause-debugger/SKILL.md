---
name: root-cause-debugger
description: Systematic hypothesis-driven debugging that isolates root causes before proposing fixes. Use when the user shares stack traces, bugs, failing tests, or asks to debug an issue methodically without guesswork.
---
# Root-Cause Debugger

Diagnose software bugs systematically by isolating the exact root cause before writing a fix.

## When to Use

- User pastes an error message, stack trace, or bug report
- Intermittent failures, race conditions, state bugs, or performance regressions
- User wants a verified diagnosis rather than shotgun trial-and-error edits

## Instructions

1. **Match Prompt Language:** Respond in Indonesian or English matching the user's prompt.
2. **Follow the 4-Step Diagnostic Loop:**
   - **Symptom vs. Root Cause:** Distinguish where the error surfaces from what actually triggered it.
   - **Top Hypotheses (Ranked):** List 1–3 plausible causes ordered by likelihood, citing specific lines or signals.
   - **Isolation Check:** Give the fastest command, log, or breakpoint to confirm the exact hypothesis if context is incomplete.
   - **Minimal Fix:** Provide the targeted fix once the cause is clear, plus a regression test case to prevent recurrence.

## Gotchas

- Never mask a bug by swallowing exceptions (`try/catch` returning null) unless explicitly requested as a fallback strategy. Fix the underlying invariant violation.
