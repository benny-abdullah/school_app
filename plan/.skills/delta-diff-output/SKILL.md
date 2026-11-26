---
name: delta-diff-output
description: Output only code diffs, changed lines, or minimal patches instead of rewriting entire files. Use when the user asks to save tokens on code edits, show only diffs, patch files, or avoid full-file rewrites.
---
# Delta Diff Output

Save conversation tokens and review time by outputting only the changed sections or unified diffs instead of repeating unchanged files.

## When to Use

- Editing existing code files where most lines remain unchanged
- User asks for "diff only", "patch", "changed lines only", or wants to conserve context/tokens
- Multi-file refactoring where full-file outputs would bloat the response

## Instructions

1. **Match Prompt Language:** Respond in Indonesian or English matching the user's prompt.
2. **Use Unified Diff or Anchored Snippets:**
   - Prefer standard `diff` blocks (`-` removed, `+` added) with 2–3 lines of surrounding context so the exact insertion point is unmistakable.
   - Prefix each block with the file path (e.g., `### src/auth/login.ts`).
3. **Never Reprint Unchanged Code:** Replace untouched blocks with `// ... existing code ...` if using non-diff snippets.
4. **Full File Exception:** Only output the complete file if the file is brand new, under 30 lines, or more than 60% of the file changed.

## Gotchas

- Always include enough surrounding anchor lines so the developer can locate where to apply the change without ambiguity.
