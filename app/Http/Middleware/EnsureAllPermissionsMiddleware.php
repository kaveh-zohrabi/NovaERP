<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Check if the authenticated user has ALL of the required permissions.
 *
 * Usage in routes:
 *   Route::middleware('permission.all:users.view,users.create')->group(function () { ... });
 */
class EnsureAllPermissionsMiddleware
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if (! $request->user()->hasAllPermissions($permissions)) {
            abort(403, 'Unauthorized. Required permissions: '.implode(', ', $permissions));
        }

        return $next($request);
    }
}
