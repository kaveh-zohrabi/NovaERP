<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
        $this->registerGates();
    }

    private function registerGates(): void
    {
        $gates = [
            'view-settings' => ['settings.view', 'settings.manage'],
            'manage-settings' => ['settings.manage'],
            'view-reports' => ['reports.view', 'reports.export'],
            'export-reports' => ['reports.export'],
            'view-sessions' => ['sessions.view'],
            'force-logout' => ['sessions.force-logout'],
            'manage-roles' => ['roles.view', 'roles.manage'],
            'manage-permissions' => ['permissions.view', 'permissions.manage'],
        ];

        foreach ($gates as $name => $permissions) {
            Gate::define($name, fn (User $user) => $this->checkPermission($user, $permissions));
        }
    }

    private function checkPermission(User $user, array $permissions): bool
    {
        try {
            return count($permissions) === 1
                ? $user->hasPermissionTo($permissions[0])
                : $user->hasAnyPermission($permissions);
        } catch (\Throwable) {
            return false;
        }
    }
}
