<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'legal_name' => fake()->company().' LLC',
            'registration_number' => fake()->numerify('#########'),
            'tax_number' => 'US-'.fake()->numerify('#########'),
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'website' => fake()->url(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'country' => fake()->countryCode(),
            'postal_code' => fake()->postcode(),
            'status' => 'active',
            'settings' => [
                'currency' => 'USD',
                'timezone' => 'America/New_York',
            ],
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['status' => 'inactive']);
    }
}
