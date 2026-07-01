<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class UserPolicy extends BasePolicy
{
    protected function permissionPrefix(): string
    {
        return 'users';
    }

    /**
     * Determine whether the user can view any users.
     *
     * Requires: users.view OR users.manage
     */
    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, 'view')
            || $this->hasPermission($user, 'manage');
    }

    /**
     * Determine whether the user can view the user.
     *
     * Requires: users.view OR users.manage OR own profile
     */
    public function view(User $user, User $model): bool
    {
        return $user->is($model)
            || $this->hasPermission($user, 'view')
            || $this->hasPermission($user, 'manage');
    }

    /**
     * Determine whether the user can create users.
     *
     * Requires: users.create OR users.manage
     */
    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'create')
            || $this->hasPermission($user, 'manage');
    }

    /**
     * Determine whether the user can update the user.
     *
     * Own profile: always allowed
     * Others: requires users.update OR users.manage
     */
    public function update(User $user, User $model): bool
    {
        return $user->is($model)
            || $this->hasPermission($user, 'update')
            || $this->hasPermission($user, 'manage');
    }

    /**
     * Determine whether the user can delete the user.
     *
     * Own account: always allowed
     * Others: requires users.delete OR users.manage
     */
    public function delete(User $user, User $model): bool
    {
        return $user->is($model)
            || $this->hasPermission($user, 'delete')
            || $this->hasPermission($user, 'manage');
    }

    /**
     * Determine whether the user can restore the user.
     *
     * Requires: users.delete OR users.manage
     */
    public function restore(User $user, User $model): bool
    {
        return $this->hasPermission($user, 'delete')
            || $this->hasPermission($user, 'manage');
    }

    /**
     * Determine whether the user can force delete the user.
     *
     * Requires: users.delete OR users.manage
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $this->hasPermission($user, 'delete')
            || $this->hasPermission($user, 'manage');
    }
}
