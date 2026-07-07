<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Dashboard;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DashboardFactory extends Factory
{
    protected $model = Dashboard::class;

    public function definition(): array
    {
        return [
            'company_id' => 1,
            'created_by' => User::factory(),
            'name' => fake()->unique()->words(2, true).' Dashboard',
            'description' => fake()->sentence(),
            'layout_configuration' => null,
        ];
    }
}
