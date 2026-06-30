<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Users
            ['name' => 'View Users',          'slug' => 'users.view',      'group' => 'users'],
            ['name' => 'Create Users',        'slug' => 'users.create',    'group' => 'users'],
            ['name' => 'Update Users',        'slug' => 'users.update',    'group' => 'users'],
            ['name' => 'Delete Users',        'slug' => 'users.delete',    'group' => 'users'],
            ['name' => 'Manage Users',        'slug' => 'users.manage',    'group' => 'users'],

            // Roles & Permissions
            ['name' => 'View Roles',          'slug' => 'roles.view',      'group' => 'roles'],
            ['name' => 'Manage Roles',        'slug' => 'roles.manage',    'group' => 'roles'],

            // Invoices
            ['name' => 'View Invoices',       'slug' => 'invoices.view',   'group' => 'invoices'],
            ['name' => 'Create Invoices',     'slug' => 'invoices.create', 'group' => 'invoices'],
            ['name' => 'Update Invoices',     'slug' => 'invoices.update', 'group' => 'invoices'],
            ['name' => 'Delete Invoices',     'slug' => 'invoices.delete', 'group' => 'invoices'],

            // Inventory
            ['name' => 'View Inventory',      'slug' => 'inventory.view',   'group' => 'inventory'],
            ['name' => 'Adjust Inventory',    'slug' => 'inventory.adjust', 'group' => 'inventory'],

            // Reports
            ['name' => 'View Reports',        'slug' => 'reports.view',    'group' => 'reports'],
            ['name' => 'Export Reports',      'slug' => 'reports.export',  'group' => 'reports'],

            // Settings
            ['name' => 'View Settings',       'slug' => 'settings.view',   'group' => 'settings'],
            ['name' => 'Manage Settings',     'slug' => 'settings.manage', 'group' => 'settings'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}
