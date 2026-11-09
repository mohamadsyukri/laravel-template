<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

// The no-escalation rule: a user hands out only access they hold themselves, and manages only users
// whose access is within their own. Before this rule, admin.users.edit alone was enough to reset the
// super-admin's password (probe on 9 November 2026: edit 200, update 302, password changed).

beforeEach(function (): void {
    seedAccess();
});

it('stops a user manager from giving anyone the super-admin role', function (): void {
    $manager = userWithPermissions('admin.users.create');

    $this->actingAs($manager)->post(route('admin.users.store'), [
        'name' => 'Ali Hassan',
        'email' => 'ali@example.com',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
        'roles' => [config('access.super_admin_role')],
        'is_active' => '1',
    ])->assertSessionHasErrors('roles.0');

    expect(User::query()->where('email', 'ali@example.com')->exists())->toBeFalse();
});

it('stops a user manager from assigning a role that grants more than their own access', function (): void {
    Role::findOrCreate('Auditor', 'web')->givePermissionTo('admin.audit-trail.index');
    $manager = userWithPermissions('admin.users.create');

    $this->actingAs($manager)->post(route('admin.users.store'), [
        'name' => 'Ali Hassan',
        'email' => 'ali@example.com',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
        'roles' => ['Auditor'],
        'is_active' => '1',
    ])->assertSessionHasErrors('roles.0');

    expect(User::query()->where('email', 'ali@example.com')->exists())->toBeFalse();
});

it('lets a user manager assign a role within their own access', function (): void {
    Role::findOrCreate('Helper', 'web')->givePermissionTo('admin.users.index');
    $manager = userWithPermissions('admin.users.create', 'admin.users.index');

    $this->actingAs($manager)->post(route('admin.users.store'), [
        'name' => 'Ali Hassan',
        'email' => 'ali@example.com',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
        'roles' => ['Helper'],
        'is_active' => '1',
    ])->assertRedirect(route('admin.users.index'));

    expect(User::query()->where('email', 'ali@example.com')->sole()->hasRole('Helper'))->toBeTrue();
});

it("stops a user manager from taking over the super-admin's account", function (): void {
    $target = superAdmin();
    $manager = userWithPermissions('admin.users.edit');

    $this->actingAs($manager)
        ->get(route('admin.users.edit', $target))
        ->assertForbidden();

    $this->actingAs($manager)
        ->put(route('admin.users.update', $target), [
            'name' => $target->name,
            'email' => $target->email,
            'password' => 'taken-over-123',
            'password_confirmation' => 'taken-over-123',
            'roles' => [config('access.super_admin_role')],
            'is_active' => '1',
        ])
        ->assertForbidden();

    expect(Hash::check('taken-over-123', $target->fresh()->password))->toBeFalse();
});

it('lets a user manager edit a user whose access is within their own', function (): void {
    Role::findOrCreate('Helper', 'web');
    $target = User::factory()->create();
    $target->assignRole('Helper');
    $manager = userWithPermissions('admin.users.edit');

    $this->actingAs($manager)->get(route('admin.users.edit', $target))->assertOk();

    $this->actingAs($manager)->put(route('admin.users.update', $target), [
        'name' => 'Renamed Helper',
        'email' => $target->email,
        'roles' => ['Helper'],
        'is_active' => '1',
    ])->assertRedirect(route('admin.users.index'));

    expect($target->fresh()->name)->toBe('Renamed Helper');
});

it('stops a role editor from granting a permission they do not hold', function (): void {
    $role = Role::findOrCreate('Clerk', 'web');
    $editor = userWithPermissions('admin.roles.edit');

    $this->actingAs($editor)->put(route('admin.roles.update', $role), [
        'name' => 'Clerk',
        'permissions' => ['admin.users.edit'],
    ])->assertSessionHasErrors('permissions.0');

    expect($role->fresh()->hasPermissionTo('admin.users.edit'))->toBeFalse();
});

it('lets the super-admin assign any role and grant any permission', function (): void {
    $admin = superAdmin();
    $role = Role::findOrCreate('Clerk', 'web');

    $this->actingAs($admin)->post(route('admin.users.store'), [
        'name' => 'Ali Hassan',
        'email' => 'ali@example.com',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
        'roles' => [config('access.super_admin_role')],
        'is_active' => '1',
    ])->assertRedirect(route('admin.users.index'));

    $this->actingAs($admin)->put(route('admin.roles.update', $role), [
        'name' => 'Clerk',
        'permissions' => ['admin.users.edit'],
    ])->assertRedirect();

    expect(User::query()->where('email', 'ali@example.com')->sole()->isSuperAdmin())->toBeTrue()
        ->and($role->fresh()->hasPermissionTo('admin.users.edit'))->toBeTrue();
});
