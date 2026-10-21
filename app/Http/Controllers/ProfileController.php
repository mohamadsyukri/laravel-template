<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * The user's own page: who they are and a change-password form (saved by Fortify's
 * user-password.update route).
 */
final class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('profile.edit');
    }
}
