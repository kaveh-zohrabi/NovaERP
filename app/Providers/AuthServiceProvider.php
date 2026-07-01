<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy map for the application.
     */
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        $this->registerGates();
    }

    /**
     * Register gates for non-resource operations.
     */
    private function registerGates(): void
    {
        Gate::define('view-settings', function (User $user) {
            try {
                return $user->hasAnyPermission(['settings.view', 'settings.manage']);
            } catch (\Throwable) {
                return false;
            }
        });

        Gate::define('manage-settings', function (User $user) {
            try {
                return $user->hasPermissionTo('settings.manage');
            } catch (\Throwable) {
                return false;
            }
        });

        Gate::define('view-reports', function (User $user) {
            try {
                return $user->hasAnyPermission(['reports.view', 'reports.export']);
            } catch (\Throwable) {
                return false;
            }
        });

        Gate::define('export-reports', function (User $user) {
            try {
                return $user->hasPermissionTo('reports.export');
            } catch (\Throwable) {
                return false;
            }
        });

        Gate::define('view-sessions', function (User $user) {
            try {
                return $user->hasPermissionTo('sessions.view');
            } catch (\Throwable) {
                return false;
            }
        });

        Gate::define('force-logout', function (User $user) {
            try {
                return $user->hasPermissionTo('sessions.force-logout');
            } catch (\Throwable) {
                return false;
            }
        });

        Gate::define('manage-roles', function (User $user) {
            try {
                return $user->hasAnyPermission(['roles.view', 'roles.manage']);
            } catch (\Throwable) {
                return false;
            }
        });

        Gate::define('manage-permissions', function (User $user) {
            try {
                return $user->hasAnyPermission(['permissions.view', 'permissions.manage']);
            } catch (\Throwable) {
                return false;
            }
        });
    }
}
