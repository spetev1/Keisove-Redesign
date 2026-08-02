# Keisove

Online store for phone cases, perfumes and accessories. Currently a **client-facing
demo** built to win a redesign pitch - every decision should keep the path open to
expanding it into the full production store without a rewrite.

# Project Environment

- **Local dev**: Laravel Sail (Docker) - always use `./sail` prefix for all commands
  (e.g. `./sail artisan migrate`, `./sail up -d`, `./sail yarn dev`, `./sail yarn build`)
- **Package manager**: Yarn - always use `yarn` instead of `npm`
- **Database**: PostgreSQL
- **Ports**: app `8080`, Vite `5273`, Postgres `5433`, Redis `6380` - offset from the
  dipz project so both can run at the same time. App is at http://localhost:8080

# Sharing the demo

The demo is shared over a Cloudflare Tunnel from the local machine (`./share`).
Two rules follow from that and must not be broken:

- **Serve built assets, never `yarn dev`** - Vite HMR does not survive the tunnel.
  Run `./sail yarn build` before sharing.
- **HTTPS scheme is forced** when `TUNNEL_ACTIVE=true`, and proxies are trusted.
  Without this Laravel emits `http://localhost` asset URLs and the page loads unstyled.

The demo is gated by `DEMO_PASSWORD` and served `noindex` so the client's unreleased
redesign is not publicly crawlable.

# Conventions

- Follow existing code conventions. Check sibling files for structure and naming before
  creating or editing a file.
- Use descriptive names for variables and methods (`isFeaturedProduct`, not `featured()`).
- Check for an existing component before writing a new one.
- Design tokens live in `resources/css/app.css` as Tailwind v4 `@theme` variables.
  Re-skinning to the client's real brand should be a single-file edit - never hardcode
  brand colours in components.
- Products, categories and brands are read from Postgres, not hardcoded in Vue. The demo
  is the foundation of the real store.

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application is a Laravel application and its main Laravel ecosystem packages &
versions are below. You are an expert with them all. Ensure you abide by these specific
packages & versions.

- php - 8.5
- inertiajs/inertia-laravel (INERTIA_LARAVEL) - v3
- laravel/fortify (FORTIFY) - v1
- laravel/framework (LARAVEL) - v13
- laravel/wayfinder (WAYFINDER) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v12
- @inertiajs/vue3 (INERTIA_VUE) - v3
- eslint (ESLINT) - v9
- prettier (PRETTIER) - v3
- tailwindcss (TAILWINDCSS) - v4
- vue (VUE) - v3

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need
  to run `./sail yarn build` or `./sail yarn dev`. Ask them.

## Documentation Files

- Only create documentation files if explicitly requested by the user.

## Replies

- Be concise. Focus on what's important rather than explaining obvious details.

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public Cart $cart) { }`.
  Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters:
  `function isAvailable(Product $product, ?string $variant = null): bool`
- Use TitleCase for Enum keys: `PhoneCase`, `Perfume`, `Accessory`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally
  complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `./sail artisan make:` commands to create new files (migrations, controllers,
  models, etc.). Pass `--no-interaction` and the correct `--options`.
- If creating a generic PHP class, use `./sail artisan make:class`.

### Model Creation

- When creating new models, create useful factories and seeders for them too.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories. Check for custom states first.
- Most tests should be feature tests.

=== inertia-laravel/core rules ===

# Inertia

- Components live in `resources/js/pages`. Use `Inertia::render()` for server-side
  routing instead of Blade views.
- Vue components must have a single root element.

# Inertia v3

- Axios has been removed. Use the built-in XHR client, or install Axios separately.
- `Inertia::lazy()` / `LazyProp` has been removed. Use `Inertia::optional()` instead.
- When using deferred props, add an empty state with a pulsing or animated skeleton.
- Event renames: `invalid` is now `httpException`, `exception` is now `networkError`.
- `router.cancel()` replaced by `router.cancelAll()`.

=== wayfinder/core rules ===

# Laravel Wayfinder

Use Wayfinder to generate TypeScript functions for Laravel routes. Import from
`@/actions/` (controllers) or `@/routes/` (named routes).

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, run `./sail php vendor/bin/pint --dirty` before
  finalizing changes to ensure your code matches the project's expected style.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing
  test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed: `./sail artisan test --compact` with a specific
  filename or filter.
- Do NOT delete tests without approval.

</laravel-boost-guidelines>
