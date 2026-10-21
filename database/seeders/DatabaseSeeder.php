<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Reference data every environment needs. Every seeder here is idempotent: re-running it changes
 * nothing that is already in place, so `db:seed` is safe on a live database.
 */
class DatabaseSeeder extends Seeder
{
    // Seeding is setup, not user activity: keep it out of the audit trail.
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            MenuSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
