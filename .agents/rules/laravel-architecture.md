---
trigger: glob
globs: "app/**/*.php, routes/**/*.php, database/**/*.php, config/**/*.php"
description: "Laravel architecture, PHP style, Eloquent, queue, Livewire, and Filament rules for this project."
---

# Laravel Architecture Rules

## Structure
- Domain code lives in `app/Domain/<Domain>/`. Do not create new top-level domains without asking.
- Controllers, Livewire components, and Filament resources are thin: validate, call an Action,
  return a response.
- Business logic goes in Action classes: one class, one purpose, one public `handle()` method,
  dependencies via constructor injection. Examples: `PublishArticle`, `CreateArticleTranslation`,
  `RecordArticleView`.
- Validation lives in Form Requests (or Livewire/Filament validation for those layers).
  Authorization lives in Policies or Filament panel access checks.

## PHP
- `declare(strict_types=1);` in every PHP file you create. Typed properties, parameters, and returns.
- Use backed enums for fixed sets (article status, locale).
- Prefer `final` classes for Actions and value objects.
- Use `config()` in application code. Call `env()` only inside `config/*.php`.
- No catch-all helper files. Put code in a named class.

## Eloquent and database
- Declare `$fillable` explicitly. Never `$guarded = []`.
- Prevent N+1: eager load relations and keep `Model::preventLazyLoading()` on outside production.
- Enforce integrity in the database (unique indexes, foreign keys from `docs/02-technical-design.md`
  section 4.2), not only in validation.
- Never edit a migration that was committed or deployed. Add a new one.
- Complex reads go into a dedicated query class. Simple scopes may stay on the model.
- Do not introduce the repository pattern. Do not add an interface unless there are two
  implementations or the docs require it (for example `TranslatorInterface`).

## Jobs and queues
- Jobs must be idempotent and safe to retry. Set `tries` and `backoff` explicitly.
- Dispatch jobs from Actions, not from models or controllers.
- Log failures without sensitive data.

## Livewire and Filament
- Livewire components are small, contain no business logic, validate public properties, and avoid
  large payloads in public properties.
- Filament resources stay declarative. Custom behavior calls Actions.
- Admin access is limited to the single `admin` role.

## Simplicity check before finishing
Could this be done with fewer files, classes, or lines without losing clarity? If yes, simplify.
