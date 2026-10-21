<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

final readonly class UserService
{
    /**
     * @return Builder<User>
     */
    public function listingQuery(): Builder
    {
        return User::query()->with('roles:id,name');
    }

    /**
     * @param  array{name: string, email: string, password: string, is_active: bool, roles: list<string>}  $data
     */
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data): User {
            $user = User::query()->create(Arr::except($data, ['roles']));
            $user->syncRoles($data['roles']);

            return $user;
        });
    }

    /**
     * @param  array{name: string, email: string, password: ?string, is_active: bool, roles: list<string>}  $data
     */
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data): User {
            $attributes = Arr::except($data, ['roles', 'password']);

            // A blank password on the edit form means "keep the current one".
            if (filled($data['password'])) {
                $attributes['password'] = $data['password'];
            }

            $user->update($attributes);
            $user->syncRoles($data['roles']);

            return $user;
        });
    }
}
