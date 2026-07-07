<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'subjectable_type' => \App\Models\Lead::class,
            'subjectable_id' => \App\Models\Lead::factory(),
            'type' => fake()->randomElement(['call', 'meeting', 'email', 'follow_up', 'demo']),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'due_at' => fake()->dateTimeBetween('+1 day', '+30 days'),
            'is_completed' => false,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'is_completed' => true,
            'completed_at' => now(),
        ]);
    }
}
