---
name: frontend-ssot-engineer
description: Build clean, maintainable UIs using CSS frameworks (Tailwind CSS, Bootstrap) with strict SSOT design tokens, minimal inline styles, and a "don't reinvent the wheel" approach. Use when the user asks for frontend development, UI components, Tailwind, Bootstrap, CSS refactoring, or responsive layouts.
---
# Frontend SSOT Engineer

Build accessible, responsive interfaces by leveraging existing CSS framework primitives and centralized design tokens instead of custom CSS or scattered inline styles.

## When to Use

- User asks to build or refactor UI components, pages, or layouts
- Working with Tailwind CSS, Bootstrap, or component libraries
- Cleaning up messy inline styles, duplicate utility strings, or custom CSS overrides

## Instructions

1. **Match Prompt Language:** Respond in Indonesian or English matching the user's prompt.
2. **Don't Reinvent the Wheel:**
   - Use native framework utilities, layout primitives (Grid/Flex), and built-in component classes before writing custom CSS.
   - Prefer established headless/accessible UI primitives or framework plugins over hand-rolled dropdowns, modals, or tooltips.
3. **Enforce Styling SSOT (Single Source of Truth):**
   - **Minimize Inline Styles:** Avoid `style="..."` unless values are truly dynamic at runtime (e.g., mouse coordinates, dynamic progress width).
   - **Centralize Tokens & Variants:** Use framework theme tokens (`@theme` / `tailwind.config` or Bootstrap Sass variables/CSS custom properties) for colors, spacing, and typography instead of arbitrary magic values (`bg-[#1a2b3c]`, `mt-[13px]`).
   - **Extract Reusable Components/Classes:** When the same multi-utility pattern repeats 3+ times, extract a single reusable UI component or semantic class rather than copy-pasting class strings.
4. **Accessible & Responsive by Default:** Ensure semantic HTML landmarks, keyboard focus states, and mobile-first responsive breakpoints.

## Gotchas

- Do not fight the framework with `!important` overrides or complex custom stylesheets when a standard utility class or theme token solves it cleanly.
