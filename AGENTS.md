# Repository Guidelines

## Project Structure & Module Organization
The Laravel 12 core lives in `app/`, where HTTP controllers, Livewire UI components, exports, listeners, and Eloquent models are grouped by responsibility. Configuration is in `config/`, while database migrations, factories, and seeders reside under `database/`. Views and Tailwind assets live in `resources/`; built files publish to `public/`. Legacy export scripts stay at the repository root. Feature-level tests are collected in `tests/Feature`, with granular units in `tests/Unit`.

## Build, Test, and Development Commands
- `composer install && npm install` prepares PHP and front-end dependencies.
- `composer dev` runs the local stack (Laravel HTTP server, queue listener, log tailing, and Vite) via `concurrently`.
- `php artisan serve` and `npm run dev` can be launched separately when you only need one runtime.
- `npm run build` compiles production-ready assets with Vite.
- `composer test` clears config cache and executes the full PHPUnit suite; prefer it for CI parity.

## Coding Style & Naming Conventions
Follow PSR-12 for PHP (4-space indentation, brace-on-next-line). Run `vendor/bin/pint` before submitting changes; it enforces Laravel Pint defaults and fixes common issues automatically. Prefer singular, StudlyCase model and Livewire component names (e.g., `PerusahaanExport`). Route names stay kebab-case, while Blade and Livewire templates live in snake_case directories.

## Testing Guidelines
Add or update tests alongside code changes. Place end-to-end scenarios in `tests/Feature` and isolate pure logic in `tests/Unit`. Name test methods with intent (`test_pengajuan_export_handles_empty_filters`). Use `php artisan test --filter=Export` during iteration, but run the full `composer test` suite before opening a pull request. Cover new behaviour and note any gaps in the PR.

## Commit & Pull Request Guidelines
Write imperative, present-tense commit subjects that mirror existing history (`Add Pengajuan excel export guard`). Group related changes into a single commit when practical. Pull requests should describe the problem, outline the solution, list manual verification steps, and link the relevant issue or ticket. Call out migrations or ENV changes so reviewers can prepare their environment.

## Environment & Configuration Notes
Copy `.env.example` to `.env`, set database credentials, then run `php artisan key:generate` and `php artisan migrate --seed` if seeders are available. Use `storage/` for temporary exports; never commit generated Excel files. Queue workers depend on Redis via Predis—confirm `QUEUE_CONNECTION` before enabling background jobs. Remove sensitive credentials from logs and uploaded artifacts before sharing.
