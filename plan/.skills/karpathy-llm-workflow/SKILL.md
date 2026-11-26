---
name: karpathy-llm-workflow
description: Enforce Andrej Karpathy's spec-first, iterative, verifiable AI coding loop. Use when the user mentions "karpathy workflow", "spec first", "verifiable loop", or wants to plan, decompose, and verify features systematically before generating large code blocks.
---
# Karpathy LLM Workflow

Execute software tasks using a disciplined spec-first, small-step, and verification-driven loop rather than one-shotting large code dumps.

## When to Use

- User invokes "karpathy workflow" or "spec first"
- Building a non-trivial feature, pipeline, or refactor with AI assistance
- Preventing AI scope creep, silent regressions, and unverified code generation

## Instructions

1. **Match Prompt Language:** Respond in Indonesian or English matching the user's prompt.
2. **Phase 1 — Spec & Contract First (No Full Implementation Yet):**
   - Clarify ambiguous requirements and define exact inputs, outputs, invariants, and edge cases.
   - Propose a step-by-step plan of small, isolated milestones.
   - Pause for user confirmation if assumptions affect architecture or behavior.
3. **Phase 2 — Incremental Execution:**
   - Implement one small, testable slice at a time.
4. **Phase 3 — Mandatory Verification Loop:**
   - Pair every code change with concrete verification criteria: unit assertions, test commands, or expected input/output traces the user can immediately run to prove correctness.

## Gotchas

- Do not dump 500 lines of unverified code in turn one. Lock the specification and verification criteria first.
