<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'Administrator')->first();
        $managerRole = Role::where('name', 'Manager')->first();
        $employeeRole = Role::where('name', 'Employee')->first();

        if (! $adminRole || ! $managerRole || ! $employeeRole) {
            $this->command->error('Roles not found. Run RoleSeeder first.');

            return;
        }

        // ──────────────────────────────────────────────
        // Administrator
        // ──────────────────────────────────────────────
        // Full system access. Can manage all modules,
        // users, roles, permissions, and settings.
        $admin = User::updateOrCreate(
            ['email' => 'admin@novaerp.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'status' => UserStatus::Active,
                'email_verified_at' => now(),
            ]
        );

        if (! $admin->hasRole('Administrator')) {
            $admin->assignRole($adminRole);
        }

        // ──────────────────────────────────────────────
        // Manager
        // ──────────────────────────────────────────────
        // Day-to-day operations. Can manage users,
        // invoices, inventory, and reports.
        $manager = User::updateOrCreate(
            ['email' => 'manager@novaerp.com'],
            [
                'name' => 'Manager',
                'password' => Hash::make('password'),
                'status' => UserStatus::Active,
                'email_verified_at' => now(),
            ]
        );

        if (! $manager->hasRole('Manager')) {
            $manager->assignRole($managerRole);
        }

        // ──────────────────────────────────────────────
        // Employee
        // ──────────────────────────────────────────────
        // Standard user. Can view and create invoices,
        // view inventory, and view reports.
        $employee = User::updateOrCreate(
            ['email' => 'employee@novaerp.com'],
            [
                'name' => 'Employee',
                'password' => Hash::make('password'),
                'status' => UserStatus::Active,
                'email_verified_at' => now(),
            ]
        );

        if (! $employee->hasRole('Employee')) {
            $employee->assignRole($employeeRole);
        }
    }
}
