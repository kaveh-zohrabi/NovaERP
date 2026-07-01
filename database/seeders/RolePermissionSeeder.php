<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::where('name', 'Administrator')->first();
        $manager = Role::where('name', 'Manager')->first();
        $employee = Role::where('name', 'Employee')->first();

        if (! $admin || ! $manager || ! $employee) {
            $this->command->error('Roles not found. Run RoleSeeder first.');

            return;
        }

        // Admin gets everything
        $admin->givePermissionTo(Permission::all());

        // Manager gets operational permissions
        $manager->givePermissionTo(Permission::whereIn('name', [
            // Users
            'users.view', 'users.create', 'users.update',
            // Roles
            'roles.view',
            // Profile
            'profile.view', 'profile.update', 'profile.change-password',
            // Invoices
            'invoices.view', 'invoices.create', 'invoices.update', 'invoices.delete',
            // Inventory
            'inventory.view', 'inventory.adjust',
            // Reports
            'reports.view', 'reports.export',
        ])->get());

        // Employee gets minimal permissions
        $employee->givePermissionTo(Permission::whereIn('name', [
            // Profile
            'profile.view', 'profile.update', 'profile.change-password',
            // Invoices
            'invoices.view', 'invoices.create',
            // Inventory
            'inventory.view',
            // Reports
            'reports.view',
        ])->get());
    }
}
