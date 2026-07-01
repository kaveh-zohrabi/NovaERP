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
     *
     * Gates are used when authorization is NOT tied to a specific model.
     * Policies are used for model-based authorization (CRUD operations).
     */
    private function registerGates(): void
    {
        // ──────────────────────────────────────────────
        // Settings
        // ──────────────────────────────────────────────
        // No Settings model exists. Settings are system-wide
        // configuration, not individual records.
        Gate::define('view-settings', function (User $user) {
            return $user->hasAnyPermission(['settings.view', 'settings.manage']);
        });

        Gate::define('manage-settings', function (User $user) {
            return $user->hasPermissionTo('settings.manage');
        });

        // ──────────────────────────────────────────────
        // Reports
        // ──────────────────────────────────────────────
        // Reports are aggregates, not individual records.
        // You can't have a "Report" model with CRUD operations.
        Gate::define('view-reports', function (User $user) {
            return $user->hasAnyPermission(['reports.view', 'reports.export']);
        });

        Gate::define('export-reports', function (User $user) {
            return $user->hasPermissionTo('reports.export');
        });

        // ──────────────────────────────────────────────
        // Sessions
        // ──────────────────────────────────────────────
        // Session management is infrastructure, not a domain model.
        // You don't create/update/delete sessions like other models.
        Gate::define('view-sessions', function (User $user) {
            return $user->hasPermissionTo('sessions.view');
        });

        Gate::define('force-logout', function (User $user) {
            return $user->hasPermissionTo('sessions.force-logout');
        });

        // ──────────────────────────────────────────────
        // Roles & Permissions
        // ──────────────────────────────────────────────
        // Roles and permissions are managed as a group,
        // not as individual records with standard CRUD.
        Gate::define('manage-roles', function (User $user) {
            return $user->hasAnyPermission(['roles.view', 'roles.manage']);
        });

        Gate::define('manage-permissions', function (User $user) {
            return $user->hasAnyPermission(['permissions.view', 'permissions.manage']);
        });
    }
}
