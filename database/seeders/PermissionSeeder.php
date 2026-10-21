<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;

final class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // One permission per guarded route, straight from the route list (no hand-kept list to drift).
        Artisan::call('permissions:sync');

        // Ordinary users can read announcements; anything more is granted on the role screen.
        Role::findByName(RoleSeeder::USER_ROLE, 'web')->givePermissionTo([
            'announcements.index',
            'announcements.show',
        ]);
    }
}
