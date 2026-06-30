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
     * Anyone authenticated can view the user list.
     * For specific permission check, use 'users.view'.
     */
    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, 'view') || $this->hasPermission($user, 'manage');
    }

    /**
     * Anyone authenticated can view a user.
     */
    public function view(User $user, User $model): bool
    {
        return $this->hasPermission($user, 'view')
            || $this->hasPermission($user, 'manage')
            || $user->id === $model->id;
    }

    /**
     * Create users requires 'users.create' or 'users.manage'.
     */
    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'create') || $this->hasPermission($user, 'manage');
    }

    /**
     * Users can always update their own profile.
     * Updating others requires 'users.update' or 'users.manage'.
     */
    public function update(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return true;
        }

        return $this->hasPermission($user, 'update') || $this->hasPermission($user, 'manage');
    }

    /**
     * Users can always delete their own account.
     * Deleting others requires 'users.delete' or 'users.manage'.
     */
    public function delete(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return true;
        }

        return $this->hasPermission($user, 'delete') || $this->hasPermission($user, 'manage');
    }

    /**
     * Restore requires 'users.delete' or 'users.manage'.
     */
    public function restore(User $user, User $model): bool
    {
        return $this->hasPermission($user, 'delete') || $this->hasPermission($user, 'manage');
    }

    /**
     * Force delete requires 'users.delete' or 'users.manage'.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $this->hasPermission($user, 'delete') || $this->hasPermission($user, 'manage');
    }
}
