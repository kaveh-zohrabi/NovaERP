<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = fake()->word();

        return [
            'company_id' => Company::factory(),
            'parent_id' => null,
            'name' => ucfirst($name).' Category',
            'slug' => Str::slug($name).'-category',
            'description' => fake()->sentence(),
            'status' => 'active',
        ];
    }
}
