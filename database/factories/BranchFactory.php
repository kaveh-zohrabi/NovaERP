<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Branch>
 */
class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        $name = fake()->city().' Office';

        return [
            'company_id' => Company::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'code' => fake()->unique()->numerify('BR-###'),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'country' => fake()->countryCode(),
            'postal_code' => fake()->postcode(),
            'status' => 'active',
            'is_headquarters' => false,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['status' => 'inactive']);
    }

    public function headquarters(): static
    {
        return $this->state(fn () => ['is_headquarters' => true]);
    }
}
