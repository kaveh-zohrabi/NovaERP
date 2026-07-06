<?php

namespace Database\Factories;

use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockMovement>
 */
class StockMovementFactory extends Factory
{
    protected $model = StockMovement::class;

    public function definition(): array
    {
        return [
            'stock_id' => Stock::factory(),
            'movement_type' => fake()->randomElement(['IN', 'OUT']),
            'quantity' => fake()->numberBetween(1, 100),
            'reference_type' => fake()->optional()->randomElement(['purchase', 'sale', 'manual']),
            'reference_id' => fake()->optional()->randomNumber(),
            'notes' => fake()->optional()->sentence(),
            'performed_by' => User::factory(),
        ];
    }
}
