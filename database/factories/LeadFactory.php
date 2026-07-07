<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'company_name' => fake()->company(),
            'source' => fake()->randomElement(['website', 'referral', 'cold_call', 'social_media', 'advertisement']),
            'status' => 'new',
            'estimated_value' => fake()->randomFloat(2, 100, 100000),
        ];
    }

    public function qualified(): static
    {
        return $this->state(fn () => ['status' => 'qualified']);
    }

    public function converted(): static
    {
        return $this->state(fn () => [
            'status' => 'won',
            'converted_at' => now(),
        ]);
    }
}
