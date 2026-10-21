<?php

declare(strict_types=1);

namespace App\Support\Access;

use Illuminate\Support\Str;

/**
 * Maps a route name to the permission that guards it (config/access.php).
 */
final class PermissionName
{
    /**
     * `announcements.update` → `announcements.edit`; `admin.users.datatable` → `admin.users.index`.
     */
    public static function forRoute(string $routeName): string
    {
        $segments = explode('.', $routeName);
        $action = array_pop($segments);
        $resolved = config('access.aliases')[$action] ?? $action;

        return implode('.', [...$segments, $resolved]);
    }

    public static function isOpen(string $routeName): bool
    {
        return Str::is(config('access.open_routes'), $routeName);
    }

    /**
     * The group a permission is listed under on the role screen: everything before the action.
     */
    public static function group(string $permission): string
    {
        return Str::beforeLast($permission, '.');
    }

    public static function action(string $permission): string
    {
        return Str::afterLast($permission, '.');
    }
}
