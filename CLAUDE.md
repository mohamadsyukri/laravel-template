# Laravel Template

Starter app for admin systems. Personal coding style comes from the `coding-style` skill
([claude-style-kit](https://github.com/mohamadsyukri/claude-style-kit)); this file holds only what
this codebase knows. When the template becomes a real project, rename it here and in `.env`.

## Stack
- Laravel 13 (streamlined structure: `bootstrap/app.php`), PHP 8.4, Pest 5, Pint (Laravel preset)
- Blade + Bootstrap 5.3 + vanilla JS/jQuery; yajra DataTables for listings; Select2; Livewire 4
  only for tabs with real per-row state
- Fortify (sign in, password reset and change — no self-registration), spatie/laravel-permission 8,
  spatie/laravel-activitylog 5, maatwebsite/excel 4, barryvdh/laravel-snappy (wkhtmltopdf)

## Running things (with [docker-webstack](https://github.com/mohamadsyukri/docker-webstack))
- PHP: `docker exec -e XDEBUG_MODE=off -w /var/www/projects/<path> php84 php artisan …`
- Site: `bin/new-site.sh <name> <path> php84` in the webstack, then http://<name>.local
- First run: `composer setup` (install, key, migrate, seed, npm build) — a human runs this.
- Tests: `php artisan test --compact` — sqlite in memory, never a real database.
- Browser: `bin/playwright.sh <path>` from the webstack (e2e/ specs, Playwright 1.62).

## How the app is wired
- **Access:** every named route inside the `route.permission` group (routes/web.php) is a
  permission — config/access.php, App\Http\Middleware\AuthorizeRoute. Supporting routes fold into
  their screen: datatable/export/pdf → index, store → create, update → edit. A new route is closed
  until `php artisan permissions:sync` registers it and a role is granted it on Pentadbiran >
  Peranan. The `Pentadbir Sistem` role passes every check. Livewire actions skip route middleware,
  so they check the permission themselves (see RecipientsTab::save).
- **Sidebar:** the `menus` table (MenuSeeder), each item shown only when the user may open its
  route (App\Support\Menu\Sidebar).
- **Reference module — Pengumuman** (`announcements.*`): copy its shape for new features.
  Thin controller → `final readonly` service → Form Requests; `index` = yajra listing with filters
  and Excel/PDF export from one query; `show` = view only; `create`/`edit` share `_form/`;
  SIMPAN/TERBIT through `form_action` + `match()`; published records are frozen; one Livewire tab.
- **UI:** components in resources/views/components (`card`, `button`, `table`, `form.*`,
  `status-badge`, `breadcrumb`); the one confirm modal via `data-confirm` (plus
  `data-confirm-livewire="method"`); toasts from `session('success'|'error')` or
  `$this->dispatch('toast', message: …)`.
- **Strings:** English keys in `__()`, Malay in lang/ms.json; validation, auth and DataTables
  wording in lang/ms/*.php.
- **Audit:** models opt in with `LogsActivity` + `getActivitylogOptions()` (logOnly, never secrets);
  Pentadbiran > Jejak Audit shows before/after values.

## Adding a feature
1. Migration (schema only) + model + factory
2. Service + Form Requests + controller in app/Http/Controllers/<Area>/
3. routes/modules/<area>.php, required inside the protected group in routes/web.php
4. Views in resources/views/modules/<area>/ — index, show, create, edit, `_form/`, `_partials/`
5. `php artisan permissions:sync`, a MenuSeeder row, translations in lang/ms.json
6. Pest tests in tests/Feature/<Area>/: access gate → 404 → happy save → validation →
   transitions → child rows → delete rules → links

## Rules specific to this codebase
- `Model::shouldBeStrict()` is on outside production: eager-load, no unknown attributes.
- Date-only columns cast to `date:Y-m-d`; screens show d/m/Y.
- Seeders are idempotent (DatabaseSeeder is safe to re-run); data never goes in migrations.
- Users are deactivated, never deleted, so the audit trail always has its causer.
