<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();

        if (! $adminRole) {
            $this->command->error('Admin role not found. Run RoleSeeder first.');

            return;
        }

        $user = User::updateOrCreate(
            ['email' => 'admin@novaerp.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'status' => UserStatus::Active,
                'email_verified_at' => now(),
            ]
        );

        if (! $user->roles->contains($adminRole)) {
            $user->roles()->attach($adminRole);
        }
    }
}
