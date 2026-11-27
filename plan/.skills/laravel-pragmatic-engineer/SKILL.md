---
name: laravel-pragmatic-engineer
description: Write clean, modern, idiomatic Laravel 13 and PHP 8.4 code adhering to framework conventions, Livewire 3 reactivity, Eloquent optimization, and robust error handling. Use when building backend features, services, artisan commands, migrations, or Livewire components.
---
# Laravel Pragmatic Engineer

Write clean, robust, modern Laravel 13 and PHP 8.4 code leveraging native framework capabilities, typed properties, match expressions, and Livewire 3 instead of unnecessary third-party packages or bloated workarounds.

## When to Use

- Developing Laravel 13 backend features, Livewire 3 components, or Blade templates
- Writing Eloquent models, migrations, relationships, queries, and model scopes
- Creating Domain Services, Form Requests, Policies, Jobs, and Artisan Commands
- Implementing authentication, authorization, caching, and Redis queues

## Instructions

1. **Match Prompt Language:** Respond in Indonesian or English matching the user's prompt.
2. **Modern PHP 8.4 & Laravel 13 Idioms:**
   - Use strict typing (`declare(strict_types=1);`), constructor property promotion, `readonly` classes, enums, and `match` expressions.
   - Utilize native Laravel 13 attributes, casts methods (`casts(): array`), and UUIDv7 (`HasUuids` / `Str::uuid7()`).
   - Use Form Requests for validation; keep Controllers and Livewire actions thin by delegating business logic to Domain Services.
3. **Database & Eloquent Best Practices:**
   - Prevent N+1 queries by eager loading relationships with `with()`.
   - Use database transactions (`DB::transaction(...)`) for multi-step write operations.
   - Enforce server-side scoping and multi-tenant / multi-role data isolation using Eloquent Global Scopes and Policies.
4. **Livewire 3 & Alpine.js Harmony:**
   - Use `wire:model.live.debounce` for responsive searching without flooding the server.
   - Use `$this->dispatch(...)` for inter-component communication and events.
   - Offload micro-interactions (toggles, dropdowns, modal visibility) to Alpine.js (`x-data`, `x-show`, `x-cloak`) to minimize server roundtrips.
5. **Security & Cryptography:**
   - Never write raw SQL without parameter binding.
   - Use `Illuminate\Support\Facades\Crypt` or encrypted casts for sensitive fields (AES-256).
   - Use `Hash::make()` with Argon2id for password hashing.
   - Implement rate limiting (`RateLimiter`) on critical public and scanner endpoints.

## Gotchas

- Avoid running heavy tasks (like sending WhatsApp messages or generating large PDFs) synchronously in request lifecycles. Always dispatch them to the Redis queue (`dispatch(new SendWhatsAppNotificationJob(...))`).
