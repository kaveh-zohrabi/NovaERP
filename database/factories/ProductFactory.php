<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->words(2, true);

        return [
            'company_id' => Company::factory(),
            'category_id' => null,
            'name' => $name,
            'slug' => Str::slug($name),
            'sku' => fake()->unique()->numerify('SKU-#####'),
            'barcode' => fake()->optional()->numerify('##############'),
            'description' => fake()->sentence(),
            'cost_price' => fake()->randomFloat(2, 10, 100),
            'selling_price' => fake()->randomFloat(2, 20, 200),
            'status' => 'active',
            'metadata' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['status' => 'inactive']);
    }
}
