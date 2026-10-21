<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

/**
 * The sidebar. Items are matched by route name and group headings by label, so editing a label
 * or icon here and re-seeding updates the row instead of adding a second one.
 */
final class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $this->item(null, 'Papan Pemuka', 'bi-speedometer2', 'dashboard', 1);
        $this->item(null, 'Pengumuman', 'bi-megaphone', 'announcements.index', 2);

        $admin = Menu::query()->updateOrCreate(
            ['parent_id' => null, 'route_name' => null, 'label' => 'Pentadbiran'],
            ['icon' => 'bi-gear', 'sort' => 9],
        );
        $this->item($admin->id, 'Pengguna', 'bi-people', 'admin.users.index', 1);
        $this->item($admin->id, 'Peranan', 'bi-shield-lock', 'admin.roles.index', 2);
        $this->item($admin->id, 'Jejak Audit', 'bi-clock-history', 'admin.audit-trail.index', 3);
    }

    private function item(?int $parentId, string $label, string $icon, string $routeName, int $sort): void
    {
        Menu::query()->updateOrCreate(
            ['route_name' => $routeName],
            ['parent_id' => $parentId, 'label' => $label, 'icon' => $icon, 'sort' => $sort],
        );
    }
}
