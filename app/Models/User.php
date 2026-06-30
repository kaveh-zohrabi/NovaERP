<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserStatus;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'password',
    'employee_code',
    'avatar',
    'phone',
    'status',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'status' => UserStatus::class,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the roles assigned to this user.
     *
     * Uses polymorphic relationship (model_has_roles) so roles can be
     * assigned to any model type (User, Employee, etc.).
     *
     * Example: $user->roles returns Collection of Role models.
     */
    public function roles(): MorphToMany
    {
        return $this->morphToMany(Role::class, 'model', 'model_has_roles');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', UserStatus::Active);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', UserStatus::Inactive);
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', UserStatus::Suspended);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar) {
            return asset('storage/'.$this->avatar);
        }

        return null;
    }

    public function getInitialsAttribute(): string
    {
        $parts = explode(' ', $this->name);

        if (count($parts) >= 2) {
            return strtoupper(mb_substr($parts[0], 0, 1).mb_substr(end($parts), 0, 1));
        }

        return strtoupper(mb_substr($this->name, 0, 2));
    }

    /*
    |--------------------------------------------------------------------------
    | Role & Permission Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Check if user has a specific role.
     *
     * @param  string|Role  $role  Role slug or Role model
     */
    public function hasRole(string|Role $role): bool
    {
        $slug = $role instanceof Role ? $role->slug : $role;

        return $this->roles()->where('slug', $slug)->exists();
    }

    /**
     * Check if user has any of the given roles.
     *
     * @param  iterable<string|Role>  $roles
     */
    public function hasAnyRole(iterable $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all of the given roles.
     *
     * @param  iterable<string|Role>  $roles
     */
    public function hasAllRoles(iterable $roles): bool
    {
        foreach ($roles as $role) {
            if (! $this->hasRole($role)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if user has a specific permission.
     *
     * Looks through all roles and checks if ANY role has the permission.
     *
     * @param  string|Permission  $permission  Permission slug or Permission model
     */
    public function hasPermission(string|Permission $permission): bool
    {
        $slug = $permission instanceof Permission ? $permission->slug : $permission;

        return $this->roles()
            ->whereHas('permissions', function ($query) use ($slug) {
                $query->where('slug', $slug);
            })
            ->exists();
    }

    /**
     * Check if user has any of the given permissions.
     *
     * @param  iterable<string|Permission>  $permissions
     */
    public function hasAnyPermission(iterable $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all of the given permissions.
     *
     * @param  iterable<string|Permission>  $permissions
     */
    public function hasAllPermissions(iterable $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (! $this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get all permissions for this user (merged from all roles).
     */
    public function getAllPermissions()
    {
        return Permission::whereHas('roles', function ($query) {
            $query->whereIn('roles.id', $this->roles()->pluck('roles.id'));
        })->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === UserStatus::Active;
    }

    public function isInactive(): bool
    {
        return $this->status === UserStatus::Inactive;
    }

    public function isSuspended(): bool
    {
        return $this->status === UserStatus::Suspended;
    }

    public function canAccess(): bool
    {
        return $this->isActive();
    }

    public function recordLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }
}
