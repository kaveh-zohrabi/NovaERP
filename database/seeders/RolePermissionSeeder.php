<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::where('slug', 'admin')->first();
        $manager = Role::where('slug', 'manager')->first();
        $employee = Role::where('slug', 'employee')->first();

        if (!$admin || !$manager || !$employee) {
            $this->command->error('Roles not found. Run RoleSeeder first.');
            return;
        }

        // Admin gets everything
        $admin->permissions()->sync(Permission::all());

        // Manager gets operational permissions
        $manager->permissions()->sync(Permission::whereIn('slug', [
            // Users
            'users.view', 'users.create', 'users.update',
            // Invoices
            'invoices.view', 'invoices.create', 'invoices.update', 'invoices.delete',
            // Inventory
            'inventory.view', 'inventory.adjust',
            // Reports
            'reports.view', 'reports.export',
        ])->pluck('id'));

        // Employee gets minimal permissions
        $employee->permissions()->sync(Permission::whereIn('slug', [
            // Invoices
            'invoices.view', 'invoices.create',
            // Inventory
            'inventory.view',
            // Reports
            'reports.view',
        ])->pluck('id'));
    }
}
