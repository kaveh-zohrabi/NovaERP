<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'branch_id' => null,
            'department_id' => null,
            'position_id' => null,
            'user_id' => null,
            'employee_code' => fake()->unique()->numerify('EMP-#####'),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'date_of_birth' => fake()->dateTimeBetween('-60 years', '-20 years'),
            'hire_date' => fake()->dateTimeBetween('-10 years', 'now'),
            'termination_date' => null,
            'status' => 'active',
            'employment_type' => 'full_time',
            'salary' => fake()->numberBetween(30000, 100000),
            'avatar' => null,
            'metadata' => null,
        ];
    }

    public function terminated(): static
    {
        return $this->state(fn () => [
            'status' => 'terminated',
            'termination_date' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['status' => 'inactive']);
    }

    public function suspended(): static
    {
        return $this->state(fn () => ['status' => 'suspended']);
    }
}
