# Laravel Template

A Laravel 13 starter for admin systems, so a new project starts with the plumbing done and
features can be built from day one.

- **Bootstrap 5.3 admin layout** — sidebar, top bar, breadcrumbs, cards, form components, one
  confirm dialog, toasts. Screens in Malay (English fallback); code in English.
- **Sign in** with Fortify — password reset and change, inactive accounts blocked, no
  self-registration.
- **Access by route name** — every screen is a permission, granted to roles; the sidebar shows only
  what a user may open. One command registers new routes.
- **User and role screens** — create users, assign roles, tick permissions per screen.
- **Audit trail** — who changed what, with before/after values.
- **Excel and PDF export** — from the same query as the listing, with its filters.
- **A reference CRUD module** ("Pengumuman") showing every convention in working, tested code:
  yajra listing, view-only page, shared create/edit form, draft → publish, a Livewire tab.

Laravel 13 · PHP 8.4 · Livewire 4 · Bootstrap 5.3 · yajra DataTables 13 · spatie/laravel-permission 8 ·
spatie/laravel-activitylog 5 · Laravel Excel 4 · laravel-snappy · Pest 5

## Start a new project

1. On GitHub, click **Use this template** (or clone it and delete `.git`).
2. Put the project where your web server can serve it. With
   [docker-webstack](https://github.com/mohamadsyukri/docker-webstack):

       cd ~/docker-webstack/projects && git clone <your-new-repo> acme/myapp
       ~/docker-webstack/bin/new-site.sh myapp acme/myapp php84

3. Copy `.env.example` to `.env` and set `APP_NAME`, `APP_URL`, the database name, and
   `ADMIN_EMAIL` / `ADMIN_PASSWORD` for the first administrator. Create the database.
4. Install, migrate, seed and build:

       composer setup

   (with the webstack, run it inside the PHP container:
   `docker exec -w /var/www/projects/acme/myapp php84 composer setup`)
5. Sign in at http://myapp.local as the administrator. If `ADMIN_PASSWORD` was empty, the seeder
   printed a random password — change it under Profil.
6. Rename things: `APP_NAME`, the project name in `composer.json` and `CLAUDE.md`, and delete the
   Pengumuman module once your first real module follows its shape.

## Add a feature

1. Migration, model and factory.
2. A `final readonly` service, Form Requests and a thin controller in `app/Http/Controllers/<Area>/`.
3. Routes in `routes/modules/<area>.php`, required inside the protected group in `routes/web.php`.
4. Views in `resources/views/modules/<area>/` — `index`, `show`, `create`, `edit`, `_form/`.
5. `php artisan permissions:sync`, a row in `MenuSeeder`, Malay text in `lang/ms.json`.
6. Pest tests in `tests/Feature/<Area>/`.

The Pengumuman module (`app/Http/Controllers/Announcements`, `app/Services/Announcements`,
`resources/views/modules/announcements`, `tests/Feature/Announcements`) is the example for each step.

## How access works

Every named route inside the `route.permission` middleware group is a permission with the same
name. Supporting routes share their screen's permission — `datatable`, `export` and `pdf` use
`index`; `store` uses `create`; `update` uses `edit` — so a listing never breaks because its data
feed wasn't granted. Settings live in `config/access.php`.

    php artisan permissions:sync          # register permissions for new routes
    php artisan permissions:sync --prune  # also delete permissions whose route is gone

Grant them on **Pentadbiran → Peranan**. The `Pentadbir Sistem` role passes every check. Sidebar
items come from the `menus` table and appear only for users who may open them.

## Commands

| Do | Command |
|---|---|
| Run the tests | `php artisan test --compact` (sqlite in memory) |
| Format PHP | `vendor/bin/pint` |
| Build assets | `npm run build` (or `npm run dev` while working) |
| Browser smoke test | `npx playwright test`, or `bin/playwright.sh <path>` from the webstack |
| Re-seed reference data | `php artisan db:seed` (idempotent — safe to re-run) |

## License

MIT
