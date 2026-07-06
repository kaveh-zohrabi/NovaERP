<?php

namespace Database\Factories;

use App\Models\ChartOfAccount;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChartOfAccount>
 */
class ChartOfAccountFactory extends Factory
{
    protected $model = ChartOfAccount::class;

    public function definition(): array
    {
        $types = ['asset', 'liability', 'equity', 'revenue', 'expense'];
        $type = fake()->randomElement($types);

        return [
            'company_id' => Company::factory(),
            'parent_id' => null,
            'code' => fake()->unique()->numerify('####'),
            'name' => fake()->word().' Account',
            'type' => $type,
            'is_active' => true,
        ];
    }
}
