<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;

beforeEach(fn () => seedAccess());

it('creates one permission per screen action, folding supporting routes into their screen', function (): void {
    $names = Permission::query()->pluck('name');

    expect($names)
        ->toContain('announcements.index', 'announcements.create', 'announcements.edit', 'admin.users.index')
        // datatable/export/pdf → index, store → create, update → edit (config/access.php aliases)
        ->not->toContain('announcements.datatable', 'announcements.export', 'announcements.store', 'announcements.update')
        // open to every signed-in user, so never a permission
        ->not->toContain('dashboard', 'profile.edit');
});

it('lists stale permissions and deletes them only with --prune', function (): void {
    Permission::findOrCreate('reports.index', 'web');

    $this->artisan('permissions:sync')->expectsOutputToContain('reports.index')->assertSuccessful();
    expect(Permission::query()->where('name', 'reports.index')->exists())->toBeTrue();

    $this->artisan('permissions:sync --prune')->assertSuccessful();
    expect(Permission::query()->where('name', 'reports.index')->exists())->toBeFalse();
});

it('refuses a screen the user has no permission for', function (): void {
    $this->actingAs(User::factory()->create())
        ->get(route('announcements.index'))
        ->assertForbidden();
});

it('lets one permission open the screen and its data feed', function (): void {
    $user = userWithPermissions('announcements.index');

    $this->actingAs($user)->get(route('announcements.index'))->assertOk();
    $this->actingAs($user)->getJson(route('announcements.datatable'))->assertOk();
    $this->actingAs($user)->get(route('announcements.create'))->assertForbidden();
});

it('lets the super-admin role open everything', function (): void {
    $this->actingAs(superAdmin())->get(route('admin.users.index'))->assertOk();
});

it('opens the dashboard and profile to any signed-in user', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('dashboard'))->assertOk();
    $this->actingAs($user)->get(route('profile.edit'))->assertOk();
});

it('shows only the sidebar items the user may open', function (): void {
    $this->actingAs(userWithPermissions('announcements.index'))
        ->get(route('dashboard'))
        ->assertSee('Pengumuman')
        ->assertDontSee('Pentadbiran')
        ->assertDontSee('Jejak Audit');

    $this->actingAs(superAdmin())
        ->get(route('dashboard'))
        ->assertSee('Pentadbiran')
        ->assertSee('Jejak Audit');
});
