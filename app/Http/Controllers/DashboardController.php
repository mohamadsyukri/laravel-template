<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $mayReadAnnouncements = $request->user()->can('announcements.index');

        return view('dashboard', [
            'latestAnnouncements' => $mayReadAnnouncements
                ? Announcement::query()->published()->latest('published_at')->limit(5)->get()
                : collect(),
        ]);
    }
}
