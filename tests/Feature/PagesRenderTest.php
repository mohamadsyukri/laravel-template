<?php

use App\Models\Announcement;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

/*
 * Renders every screen once. Catches what unit-level tests miss: a deleted route name, a Blade
 * error, a missing component or translation key.
 */
beforeEach(function (): void {
    seedAccess();
    $this->actingAs(superAdmin());
});

it('renders every screen', function (string $routeName, Closure $parameters): void {
    $this->get(route($routeName, $parameters()))->assertOk();
})->with([
    'dashboard' => ['dashboard', fn (): array => []],
    'profile' => ['profile.edit', fn (): array => []],
    'announcement list' => ['announcements.index', fn (): array => []],
    'announcement add' => ['announcements.create', fn (): array => []],
    'announcement view' => ['announcements.show', fn (): array => [Announcement::factory()->hasRecipients(2)->create()]],
    'announcement edit' => ['announcements.edit', fn (): array => [Announcement::factory()->create()]],
    'user list' => ['admin.users.index', fn (): array => []],
    'user add' => ['admin.users.create', fn (): array => []],
    'user edit' => ['admin.users.edit', fn (): array => [User::factory()->create()]],
    'role list' => ['admin.roles.index', fn (): array => []],
    'role add' => ['admin.roles.create', fn (): array => []],
    'role edit' => ['admin.roles.edit', fn (): array => [Role::findByName('Pengguna', 'web')]],
    'super-admin role edit' => ['admin.roles.edit', fn (): array => [Role::findByName(config('access.super_admin_role'), 'web')]],
    'audit list' => ['admin.audit-trail.index', fn (): array => []],
    'audit view' => ['admin.audit-trail.show', fn (): array => [Activity::query()->latest('id')->firstOrFail()]],
]);

it('feeds every listing table', function (string $routeName): void {
    Announcement::factory()->create();

    $this->getJson(route($routeName, ['draw' => 1]))->assertOk()->assertJsonStructure(['data', 'recordsTotal']);
})->with(['announcements.datatable', 'admin.users.datatable', 'admin.roles.datatable', 'admin.audit-trail.datatable']);
