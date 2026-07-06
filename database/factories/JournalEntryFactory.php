<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\JournalEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JournalEntry>
 */
class JournalEntryFactory extends Factory
{
    protected $model = JournalEntry::class;

    public function definition(): array
    {
        $year = now()->format('Y');
        $sequence = fake()->unique()->numberBetween(1, 999999);

        return [
            'company_id' => Company::factory(),
            'entry_number' => "JE-1-{$year}-".str_pad((string) $sequence, 6, '0', STR_PAD_LEFT),
            'date' => fake()->dateTimeBetween('-30 days', 'now'),
            'description' => fake()->sentence(),
            'reference_type' => null,
            'reference_id' => null,
            'status' => 'draft',
        ];
    }

    public function posted(): static
    {
        return $this->state(fn () => ['status' => 'posted']);
    }
}
