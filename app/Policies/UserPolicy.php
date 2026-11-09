<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

/**
 * Found by Laravel's policy auto-discovery for App\Models\User and checked on the edit and update
 * routes. The super-admin never reaches it: Gate::before in AppServiceProvider answers first.
 */
final class UserPolicy
{
    public function update(User $actor, User $target): bool
    {
        return $actor->mayManage($target);
    }
}
