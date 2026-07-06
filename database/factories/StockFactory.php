<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Stock>
 */
class StockFactory extends Factory
{
    protected $model = Stock::class;

    public function definition(): array
    {
        $quantity = fake()->numberBetween(10, 1000);

        return [
            'product_id' => Product::factory(),
            'warehouse_id' => Warehouse::factory(),
            'quantity' => $quantity,
            'reserved_quantity' => 0,
            'available_quantity' => $quantity,
            'reorder_level' => fake()->numberBetween(5, 20),
        ];
    }
}
