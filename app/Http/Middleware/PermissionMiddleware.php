<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Check if the authenticated user has the required permission.
 *
 * Must be used with 'auth' middleware:
 *   Route::middleware(['auth', 'permission:users.view'])->group(...);
 */
class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (! $request->user()->hasAnyPermission($permissions)) {
            abort(403, 'Unauthorized. Required permission: '.implode(' or ', $permissions));
        }

        return $next($request);
    }
}
