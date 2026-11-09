<?php

declare(strict_types=1);

namespace App\Support\Access;

use App\Models\User;
use Spatie\Permission\Models\Role;

/**
 * Roles and permissions live in Spatie's pivot tables, which LogsActivity on a model never sees, so
 * every access change is written here, in the old/attributes shape the audit trail screen shows. The
 * causer is whoever is signed in (the activitylog causer resolver).
 */
final readonly class AccessAudit
{
    /**
     * @param  list<string>  $old
     * @param  list<string>  $new
     */
    public function userRoles(User $user, array $old, array $new): void
    {
        $old = $this->sorted($old);
        $new = $this->sorted($new);
        if ($old === $new) {
            return;
        }

        activity()->performedOn($user)->event('updated')
            ->withChanges(['old' => ['roles' => $old], 'attributes' => ['roles' => $new]])
            ->log('updated');
    }

    public function roleCreated(Role $role): void
    {
        activity()->performedOn($role)->event('created')
            ->withChanges(['attributes' => $this->snapshot($role)])
            ->log('created');
    }

    /**
     * @param  list<string>  $oldPermissions
     */
    public function roleUpdated(Role $role, string $oldName, array $oldPermissions): void
    {
        $old = ['name' => $oldName, 'permissions' => $this->sorted($oldPermissions)];
        $new = $this->snapshot($role);
        // Only the fields that changed, the way LogsActivity's logOnlyDirty() reports a model.
        $changed = array_keys(array_filter($new, fn (string|array $value, string $field): bool => $old[$field] !== $value, ARRAY_FILTER_USE_BOTH));
        if ($changed === []) {
            return;
        }

        activity()->performedOn($role)->event('updated')
            ->withChanges([
                'old' => array_intersect_key($old, array_flip($changed)),
                'attributes' => array_intersect_key($new, array_flip($changed)),
            ])
            ->log('updated');
    }

    /**
     * Written before the delete, while the role and its permissions can still be read.
     */
    public function roleDeleted(Role $role): void
    {
        activity()->performedOn($role)->event('deleted')
            ->withChanges(['old' => $this->snapshot($role)])
            ->log('deleted');
    }

    /**
     * @return array{name: string, permissions: list<string>}
     */
    private function snapshot(Role $role): array
    {
        return [
            'name' => $role->name,
            'permissions' => $this->sorted($role->permissions()->pluck('name')->all()),
        ];
    }

    /**
     * @param  array<array-key, string>  $names
     * @return list<string>
     */
    private function sorted(array $names): array
    {
        sort($names);

        return array_values($names);
    }
}
