<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\Access\PermissionName;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Routing\Route as RouteDefinition;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

#[Signature('permissions:sync {--prune : Also delete permissions whose route no longer exists}')]
#[Description('Create a permission for every route guarded by the route.permission middleware')]
final class SyncPermissions extends Command
{
    public function handle(PermissionRegistrar $registrar): int
    {
        $wanted = $this->guardedPermissionNames();

        $created = $wanted->reject(fn (string $name): bool => Permission::query()->where('name', $name)->exists())
            ->each(fn (string $name) => Permission::findOrCreate($name, 'web'));

        $stale = Permission::query()->whereNotIn('name', $wanted)->pluck('name');

        $this->components->info("Permissions: {$wanted->count()} in use, {$created->count()} created.");

        if ($stale->isNotEmpty()) {
            // Listed first and deleted only on request: roles may still hold these grants.
            $this->components->warn('No route uses: '.$stale->implode(', '));

            if ($this->option('prune')) {
                Permission::query()->whereIn('name', $stale)->delete();
                $this->components->info("Deleted {$stale->count()} stale permissions.");
            }
        }

        $registrar->forgetCachedPermissions();

        return self::SUCCESS;
    }

    /**
     * @return Collection<int, string>
     */
    private function guardedPermissionNames(): Collection
    {
        return collect(Route::getRoutes()->getRoutes())
            ->filter(fn (RouteDefinition $route): bool => in_array('route.permission', $route->gatherMiddleware(), true))
            ->map(fn (RouteDefinition $route): ?string => $route->getName())
            ->filter()
            ->reject(fn (string $name): bool => PermissionName::isOpen($name))
            ->map(fn (string $name): string => PermissionName::forRoute($name))
            ->unique()
            ->sort()
            ->values();
    }
}
