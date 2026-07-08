<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition(): array
    {
        return [
            'company_id' => 1,
            'user_id' => User::factory(),
            'activity_type' => fake()->randomElement(['login', 'logout', 'created', 'updated', 'deleted', 'file_uploaded']),
            'subject_type' => \App\Models\User::class,
            'subject_id' => 1,
            'description' => fake()->sentence(),
            'metadata' => null,
            'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
