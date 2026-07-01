<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // ──────────────────────────────────────────────
            // User Management
            // ──────────────────────────────────────────────

            // View user list and profiles
            'users.view',

            // Create new user accounts
            'users.create',

            // Edit user profiles (name, email, phone, status)
            'users.update',

            // Delete or deactivate user accounts
            'users.delete',

            // Full user management (wildcard for all users.* permissions)
            'users.manage',

            // ──────────────────────────────────────────────
            // Role Management
            // ──────────────────────────────────────────────

            // View roles and their assigned permissions
            'roles.view',

            // Create, edit, delete roles and assign permissions
            'roles.manage',

            // ──────────────────────────────────────────────
            // Permission Management
            // ──────────────────────────────────────────────

            // View all available permissions
            'permissions.view',

            // Create, edit, delete permissions
            'permissions.manage',

            // ──────────────────────────────────────────────
            // Profile & Authentication
            // ──────────────────────────────────────────────

            // View and edit own profile
            'profile.view',

            // Update own profile information
            'profile.update',

            // Change own password
            'profile.change-password',

            // ──────────────────────────────────────────────
            // Session Management
            // ──────────────────────────────────────────────

            // View active sessions across all users
            'sessions.view',

            // Force logout other users
            'sessions.force-logout',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web']
            );
        }
    }
}
