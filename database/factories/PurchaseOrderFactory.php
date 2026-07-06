<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    public function definition(): array
    {
        $year = now()->format('Y');
        $sequence = fake()->unique()->numberBetween(1, 99999);

        return [
            'company_id' => Company::factory(),
            'supplier_id' => Supplier::factory(),
            'warehouse_id' => Warehouse::factory(),
            'order_number' => "PO-{$year}-".str_pad((string) $sequence, 5, '0', STR_PAD_LEFT),
            'status' => 'draft',
            'total_amount' => 0,
            'notes' => fake()->optional()->sentence(),
            'order_date' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => ['status' => 'approved']);
    }
}
