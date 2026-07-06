<?php

declare(strict_types=1);

namespace Tests\Feature\Inventory;

use App\Models\Company;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockMovementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Company $company;

    private Product $product;

    private Warehouse $warehouse;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->company = Company::factory()->create();
        $this->product = Product::factory()->create(['company_id' => $this->company->id]);
        $this->warehouse = Warehouse::factory()->create(['company_id' => $this->company->id]);
    }

    public function test_stock_in_increases_quantity(): void
    {
        $response = $this->actingAs($this->user)->post(route('stock-movements.store'), [
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'movement_type' => 'IN',
            'quantity' => 50,
            'notes' => 'Initial stock',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('stock', [
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity' => 50,
        ]);
    }

    public function test_stock_out_decreases_quantity(): void
    {
        Stock::create([
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity' => 100,
            'reserved_quantity' => 0,
            'available_quantity' => 100,
            'reorder_level' => 10,
        ]);

        $response = $this->actingAs($this->user)->post(route('stock-movements.store'), [
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'movement_type' => 'OUT',
            'quantity' => 30,
            'notes' => 'Sale',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('stock', [
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity' => 70,
        ]);
    }

    public function test_stock_out_fails_when_insufficient(): void
    {
        Stock::create([
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity' => 10,
            'reserved_quantity' => 0,
            'available_quantity' => 10,
            'reorder_level' => 5,
        ]);

        $response = $this->actingAs($this->user)->post(route('stock-movements.store'), [
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'movement_type' => 'OUT',
            'quantity' => 50,
            'notes' => 'Sale',
        ]);

        $response->assertSessionHas('error');
    }

    public function test_stock_adjustment_works(): void
    {
        Stock::create([
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity' => 100,
            'reserved_quantity' => 0,
            'available_quantity' => 100,
            'reorder_level' => 10,
        ]);

        $response = $this->actingAs($this->user)->post(route('stock-movements.store'), [
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'movement_type' => 'ADJUSTMENT',
            'quantity' => -5,
            'notes' => 'Inventory count correction',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('stock', [
            'product_id' => $this->product->id,
            'quantity' => 95,
        ]);
    }

    public function test_adjustment_requires_notes(): void
    {
        Stock::create([
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity' => 100,
            'reserved_quantity' => 0,
            'available_quantity' => 100,
            'reorder_level' => 10,
        ]);

        $response = $this->actingAs($this->user)->post(route('stock-movements.store'), [
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'movement_type' => 'ADJUSTMENT',
            'quantity' => -5,
        ]);

        $response->assertSessionHasErrors('notes');
    }

    public function test_movement_records_user(): void
    {
        Stock::create([
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity' => 100,
            'reserved_quantity' => 0,
            'available_quantity' => 100,
            'reorder_level' => 10,
        ]);

        $this->actingAs($this->user)->post(route('stock-movements.store'), [
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'movement_type' => 'OUT',
            'quantity' => 10,
            'notes' => 'Test',
        ]);

        $movement = StockMovement::latest()->first();

        $this->assertEquals($this->user->id, $movement->performed_by);
    }
}
