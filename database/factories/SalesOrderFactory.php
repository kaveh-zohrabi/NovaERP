<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Customer;
use App\Models\SalesOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalesOrder>
 */
class SalesOrderFactory extends Factory
{
    protected $model = SalesOrder::class;

    public function definition(): array
    {
        $year = now()->format('Y');
        $sequence = fake()->unique()->numberBetween(1, 99999);

        return [
            'company_id' => Company::factory(),
            'customer_id' => Customer::factory(),
            'order_number' => "SO-{$year}-".str_pad((string) $sequence, 5, '0', STR_PAD_LEFT),
            'status' => 'draft',
            'order_date' => fake()->dateTimeBetween('-30 days', 'now'),
            'total_amount' => 0,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn () => ['status' => 'confirmed']);
    }
}
