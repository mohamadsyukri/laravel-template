<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Support\Menu\Sidebar;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Fail loud outside production: lazy loading, unknown attributes and missing attributes
        // throw instead of silently costing queries or dropping data.
        Model::shouldBeStrict(! $this->app->isProduction());

        Gate::before(fn (User $user, string $ability): ?bool => $user->hasRole(config('access.super_admin_role')) ? true : null);

        ViewFacade::composer('layouts._sidebar', function (View $view): void {
            $view->with('menuItems', $this->app->make(Sidebar::class)->forUser(auth()->user()));
        });
    }
}
