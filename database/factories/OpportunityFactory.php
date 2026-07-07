<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Opportunity;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use Illuminate\Database\Eloquent\Factories\Factory;

class OpportunityFactory extends Factory
{
    protected $model = Opportunity::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'pipeline_id' => Pipeline::factory(),
            'pipeline_stage_id' => PipelineStage::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'expected_value' => fake()->randomFloat(2, 1000, 500000),
            'probability' => fake()->numberBetween(10, 90),
            'expected_closing_date' => fake()->dateTimeBetween('+1 month', '+6 months'),
            'status' => 'open',
        ];
    }

    public function won(): static
    {
        return $this->state(fn () => ['status' => 'won', 'probability' => 100]);
    }

    public function lost(): static
    {
        return $this->state(fn () => [
            'status' => 'lost',
            'probability' => 0,
            'lost_reason' => 'Competitor offer',
        ]);
    }
}
