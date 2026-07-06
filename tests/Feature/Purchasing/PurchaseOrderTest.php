<?php

declare(strict_types=1);

namespace Tests\Feature\Purchasing;

use App\Models\Company;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseOrderTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Company $company;
    private Supplier $supplier;
    private Warehouse $warehouse;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->company = Company::factory()->create();
        $this->supplier = Supplier::factory()->create(['company_id' => $this->company->id]);
        $this->warehouse = Warehouse::factory()->create(['company_id' => $this->company->id]);
    }

    public function test_index_displays_orders(): void
    {
        PurchaseOrder::factory()->create([
            'company_id' => $this->company->id,
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('purchasing-orders.index'));

        $response->assertOk();
    }

    public function test_order_can_be_created(): void
    {
        $response = $this->actingAs($this->user)->post(route('purchasing-orders.store'), [
            'company_id' => $this->company->id,
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'order_date' => now()->toDateString(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('purchase_orders', ['status' => 'draft']);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('purchasing-orders.store'), []);

        $response->assertSessionHasErrors(['company_id', 'supplier_id', 'warehouse_id', 'order_date']);
    }

    public function test_order_can_be_approved(): void
    {
        $order = PurchaseOrder::factory()->create([
            'company_id' => $this->company->id,
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
        ]);

        $response = $this->actingAs($this->user)->patch(route('purchasing-orders.approve', $order));

        $response->assertRedirect();
        $this->assertDatabaseHas('purchase_orders', ['id' => $order->id, 'status' => 'approved']);
    }

    public function test_order_can_be_cancelled(): void
    {
        $order = PurchaseOrder::factory()->create([
            'company_id' => $this->company->id,
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
        ]);

        $response = $this->actingAs($this->user)->patch(route('purchasing-orders.cancel', $order));

        $response->assertRedirect();
        $this->assertDatabaseHas('purchase_orders', ['id' => $order->id, 'status' => 'cancelled']);
    }
}
