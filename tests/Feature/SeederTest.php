<?php

use App\Models\Menu;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

it('can run twice without adding or changing anything', function (): void {
    $this->seed();
    $counts = [Role::query()->count(), Permission::query()->count(), Menu::query()->count(), User::query()->count()];
    $adminHash = User::query()->sole()->password;

    $this->seed();

    expect([Role::query()->count(), Permission::query()->count(), Menu::query()->count(), User::query()->count()])->toBe($counts)
        ->and(User::query()->sole()->password)->toBe($adminHash);
});

it('creates the first administrator with the super-admin role', function (): void {
    config(['access.admin.email' => 'pentadbir@example.com', 'access.admin.password' => 'rahsia-mula-123']);

    $this->seed();

    $admin = User::query()->where('email', 'pentadbir@example.com')->sole();
    expect($admin->hasRole(config('access.super_admin_role')))->toBeTrue()
        ->and($admin->is_active)->toBeTrue();
});
