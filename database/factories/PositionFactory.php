<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Position>
 */
class PositionFactory extends Factory
{
    protected $model = Position::class;

    public function definition(): array
    {
        $name = fake()->jobTitle();

        return [
            'department_id' => Department::factory(),
            'company_id' => Company::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'code' => fake()->unique()->numerify('POS-###'),
            'description' => fake()->sentence(),
            'min_salary' => fake()->numberBetween(30000, 50000),
            'max_salary' => fake()->numberBetween(60000, 100000),
            'status' => 'active',
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['status' => 'inactive']);
    }
}
