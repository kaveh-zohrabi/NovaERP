<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

abstract class BasePolicy
{
    use HandlesAuthorization;

    /**
     * Permission slug prefix for this policy.
     *
     * Each policy defines its prefix (e.g., 'users', 'invoices').
     * Methods then check '{prefix}.{action}' (e.g., 'users.view').
     */
    abstract protected function permissionPrefix(): string;

    /**
     * Run before all authorization checks.
     *
     * Super admins bypass all permission checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return null;
    }

    /**
     * Check if user has a specific permission.
     */
    protected function hasPermission(User $user, string $action): bool
    {
        $permission = $this->permissionPrefix().'.'.$action;

        return $user->hasPermission($permission);
    }
}
