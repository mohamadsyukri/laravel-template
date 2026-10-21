<?php

declare(strict_types=1);

namespace App\Support\Menu;

use App\Models\Menu;
use App\Models\User;
use App\Support\Access\PermissionName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * The sidebar for one user: menu items they may open, with group headings kept only when at least
 * one child survives.
 */
final readonly class Sidebar
{
    public function __construct(private Request $request) {}

    /**
     * @return list<array{label: string, icon: ?string, url: ?string, active: bool, children: list<array{label: string, icon: ?string, url: ?string, active: bool, children: array{}}>}>
     */
    public function forUser(User $user): array
    {
        $roots = Menu::query()->whereNull('parent_id')->with('children')->orderBy('sort')->get();

        $items = [];
        foreach ($roots as $menu) {
            if ($menu->route_name === null) {
                $children = $menu->children
                    ->filter(fn (Menu $child): bool => $this->mayOpen($user, $child))
                    ->map(fn (Menu $child): array => $this->item($child, []))
                    ->values()
                    ->all();

                if ($children !== []) {
                    $items[] = $this->item($menu, $children);
                }
            } elseif ($this->mayOpen($user, $menu)) {
                $items[] = $this->item($menu, []);
            }
        }

        return $items;
    }

    private function mayOpen(User $user, Menu $menu): bool
    {
        // A menu row whose route was removed from the code is skipped instead of breaking the page.
        if ($menu->route_name === null || ! Route::has($menu->route_name)) {
            return false;
        }

        return PermissionName::isOpen($menu->route_name)
            || $user->can(PermissionName::forRoute($menu->route_name));
    }

    /**
     * @param  list<array<string, mixed>>  $children
     * @return array{label: string, icon: ?string, url: ?string, active: bool, children: list<array<string, mixed>>}
     */
    private function item(Menu $menu, array $children): array
    {
        // `announcements.index` stays highlighted on every announcements.* page (show, edit, ...).
        $section = $menu->route_name === null ? null : Str::beforeLast($menu->route_name, '.');

        return [
            'label' => $menu->label,
            'icon' => $menu->icon,
            'url' => $menu->route_name === null ? null : route($menu->route_name),
            'active' => $section !== null && ($this->request->routeIs($menu->route_name) || $this->request->routeIs($section.'.*')),
            'children' => $children,
        ];
    }
}
