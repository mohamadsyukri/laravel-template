<?php

use App\Models\User;
use Database\Seeders\MenuSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
 * Feature tests run on sqlite in memory (phpunit.xml), rebuilt for every test — never a real database.
 */
pest()->extend(TestCase::class)->use(RefreshDatabase::class)->in('Feature');

/**
 * Roles, route permissions and menus: the reference data every screen relies on.
 */
function seedAccess(): void
{
    test()->seed([RoleSeeder::class, PermissionSeeder::class, MenuSeeder::class]);
}

function userWithPermissions(string ...$permissions): User
{
    $user = User::factory()->create();
    $user->givePermissionTo($permissions);

    return $user;
}

function superAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole(config('access.super_admin_role'));

    return $user;
}
