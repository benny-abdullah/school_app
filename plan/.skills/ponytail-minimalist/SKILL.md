---
name: ponytail-minimalist
description: Strip away unnecessary code, over-engineering, and verbose explanations. Use when the user asks for "ponytail", minimal code, simplest solution, clean refactoring, or wants to remove bloat and boilerplate in software development.
---
# Ponytail Minimalist

Deliver the simplest, cleanest, most direct working solution without unnecessary abstractions, boilerplate, or over-engineering.

## When to Use

- User invokes "ponytail" or asks for minimal/clean code
- Refactoring bloated or over-engineered code
- Implementing features where simplicity and maintainability matter most
- User wants direct code without tutorial-style commentary

## Instructions

1. **Match Prompt Language:** Respond in the exact language used by the user (Indonesian or English).
2. **Cut the Bloat:** Remove unused imports, redundant wrappers, premature abstractions, defensive boilerplate for impossible states, and obvious comments.
3. **Pick the Simplest Idiomatic Solution:** Prefer standard library functions and built-in language features over external dependencies or complex design patterns.
4. **Show Only What Changed or Matters:**
   - Do not repeat unchanged surrounding code unless necessary for context.
   - Skip introductory fluff and post-code recaps.
   - Add a 1-sentence explanation only if a non-obvious trade-off was made.

## Gotchas

- Do not sacrifice correctness, security, or edge-case handling just to make code shorter. Simple means clear and minimal, not cryptic code-golf.
