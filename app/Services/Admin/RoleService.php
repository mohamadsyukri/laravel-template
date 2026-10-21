<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\Menu;
use App\Support\Access\PermissionName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

final readonly class RoleService
{
    /**
     * @return Builder<Role>
     */
    public function listingQuery(): Builder
    {
        return Role::query()->withCount(['users', 'permissions']);
    }

    /**
     * @param  array{name: string, permissions: list<string>}  $data
     */
    public function create(array $data): Role
    {
        return DB::transaction(function () use ($data): Role {
            $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
            $role->syncPermissions($data['permissions']);

            return $role;
        });
    }

    /**
     * @param  array{name: string, permissions: list<string>}  $data
     */
    public function update(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data): Role {
            $role->update(['name' => $data['name']]);
            $role->syncPermissions($data['permissions']);

            return $role;
        });
    }

    /**
     * Why this role can't be deleted, or null when it can.
     */
    public function deletionBlocker(Role $role): ?string
    {
        if ($this->isSuperAdmin($role)) {
            return __('The :role role cannot be deleted.', ['role' => $role->name]);
        }

        if ($role->users()->exists()) {
            return __('This role still has users. Move them to another role first.');
        }

        return null;
    }

    public function delete(Role $role): void
    {
        $role->delete();
    }

    public function isSuperAdmin(Role $role): bool
    {
        return $role->name === config('access.super_admin_role');
    }

    /**
     * Permissions grouped the way users see the app: each group is titled with its sidebar label
     * (`announcements` → "Pengumuman"), falling back to a readable form of the route prefix.
     *
     * @return array<string, array{label: string, permissions: array<string, string>}>
     */
    public function permissionGroups(): array
    {
        $menuLabels = Menu::query()->whereNotNull('route_name')->pluck('label', 'route_name');
        $actionLabels = config('access.action_labels');

        return Permission::query()->orderBy('name')->pluck('name')
            ->groupBy(fn (string $name): string => PermissionName::group($name))
            ->map(fn (Collection $names, string $group): array => [
                'label' => $menuLabels[$group.'.index'] ?? Str::headline(str_replace('.', ' ', $group)),
                'permissions' => $names->mapWithKeys(function (string $name) use ($actionLabels): array {
                    $action = PermissionName::action($name);

                    return [$name => __($actionLabels[$action] ?? Str::headline($action))];
                })->all(),
            ])
            ->all();
    }
}
