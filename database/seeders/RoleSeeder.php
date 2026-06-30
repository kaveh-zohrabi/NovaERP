<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrator',
                'slug' => 'admin',
                'description' => 'Full system access. Can manage all modules, users, and settings.',
                'is_system' => true,
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Can manage day-to-day operations. Has access to most modules except system settings.',
                'is_system' => false,
            ],
            [
                'name' => 'Employee',
                'slug' => 'employee',
                'description' => 'Standard user with limited access. Can view and perform assigned tasks only.',
                'is_system' => false,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}
