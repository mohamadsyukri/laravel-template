<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Access\PermissionName;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Every named route in this group is guarded by the permission of the same name (after aliases).
 * Secure by default: a new route is closed until a role is granted its permission.
 */
final class AuthorizeRoute
{
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();

        // An unnamed route here can't be granted to anyone, so it is a mistake — fail loudly.
        abort_if($routeName === null, 403, 'Route has no name, so no permission can open it.');

        if (! PermissionName::isOpen($routeName)) {
            abort_unless($request->user()?->can(PermissionName::forRoute($routeName)), 403);
        }

        return $next($request);
    }
}
