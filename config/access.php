<?php

declare(strict_types=1);

/*
 * Route-name permissions: every named route inside the `route.permission` middleware group is a
 * permission (App\Http\Middleware\AuthorizeRoute). `php artisan permissions:sync` registers them.
 */
return [

    // This role passes every permission check (Gate::before in AppServiceProvider).
    'super_admin_role' => 'Pentadbir Sistem',

    // Routes any signed-in user may open without a permission (Str::is patterns).
    'open_routes' => [
        'dashboard',
        'profile.*',
    ],

    // Supporting actions share the permission of the screen they belong to, so a listing's data
    // feed or a form's submit never needs its own grant — forgetting one used to 403 a whole page.
    'aliases' => [
        'datatable' => 'index',
        'export' => 'index',
        'pdf' => 'index',
        'store' => 'create',
        'update' => 'edit',
    ],

    // Labels on the role screen, by the last segment of a permission name (translated in lang/).
    'action_labels' => [
        'index' => 'List',
        'show' => 'View',
        'create' => 'Add',
        'edit' => 'Edit',
        'destroy' => 'Delete',
    ],

    // The first administrator, created by AdminUserSeeder. An empty password gets a random one,
    // printed once by the seeder.
    'admin' => [
        'name' => env('ADMIN_NAME', 'Pentadbir Sistem'),
        'email' => env('ADMIN_EMAIL', 'admin@example.com'),
        'password' => env('ADMIN_PASSWORD'),
    ],
];
