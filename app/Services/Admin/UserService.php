<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\User;
use App\Support\Access\AccessAudit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final readonly class UserService
{
    public function __construct(private AccessAudit $audit) {}

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
            $this->audit->userRoles($user, [], $data['roles']);

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

            $wasActive = $user->is_active;
            $user->fill($attributes);
            // Deactivation also ends every "Remember me" sign-in: the cookie no longer matches a new token.
            if ($wasActive && ! $user->is_active) {
                $user->setRememberToken(Str::random(60));
            }
            $user->save();
            $oldRoles = $user->roles()->pluck('name')->all();
            $user->syncRoles($data['roles']);
            $this->audit->userRoles($user, $oldRoles, $data['roles']);

            return $user;
        });
    }
}
