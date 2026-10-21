<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

final class RoleSeeder extends Seeder
{
    public const string USER_ROLE = 'Pengguna';

    public function run(): void
    {
        Role::findOrCreate(config('access.super_admin_role'), 'web');
        Role::findOrCreate(self::USER_ROLE, 'web');
    }
}
