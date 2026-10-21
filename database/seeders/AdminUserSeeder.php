<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $config = config('access.admin');
        $user = User::query()->where('email', $config['email'])->first();

        if ($user === null) {
            $password = $config['password'] ?: Str::password(16);
            $user = User::query()->create([
                'name' => $config['name'],
                'email' => $config['email'],
                'password' => $password,
                'is_active' => true,
            ]);

            if (! $config['password']) {
                $this->command?->warn("Created {$user->email} with password {$password} — change it after signing in.");
            }
        }

        // Re-running never resets an existing admin's password; it only makes sure the role is there.
        $user->assignRole(config('access.super_admin_role'));
    }
}
