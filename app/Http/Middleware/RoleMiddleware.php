<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Check if the authenticated user has the required role.
 *
 * Must be used with 'auth' middleware:
 *   Route::middleware(['auth', 'role:Administrator'])->group(...);
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()->hasAnyRole($roles)) {
            abort(403, 'Unauthorized. Required role: '.implode(' or ', $roles));
        }

        return $next($request);
    }
}
