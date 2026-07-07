<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserNotificationFactory extends Factory
{
    protected $model = UserNotification::class;

    public function definition(): array
    {
        return [
            'company_id' => 1,
            'user_id' => User::factory(),
            'type' => fake()->randomElement(['inventory', 'sales', 'purchasing', 'accounting', 'crm']),
            'title' => fake()->sentence(3),
            'message' => fake()->paragraph(),
            'data' => null,
            'priority' => fake()->randomElement(['low', 'normal', 'high', 'urgent']),
            'status' => 'unread',
        ];
    }

    public function read(): static
    {
        return $this->state(fn () => [
            'status' => 'read',
            'read_at' => now(),
        ]);
    }
}
