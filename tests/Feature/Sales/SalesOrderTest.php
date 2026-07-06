<?php

declare(strict_types=1);

namespace Tests\Feature\Sales;

use App\Models\Company;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesOrderTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Company $company;
    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->company = Company::factory()->create();
        $this->customer = Customer::factory()->create(['company_id' => $this->company->id]);
    }

    public function test_index_displays_orders(): void
    {
        SalesOrder::factory()->create([
            'company_id' => $this->company->id,
            'customer_id' => $this->customer->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('orders.index'));

        $response->assertOk();
        $response->assertSee('Sales Orders');
    }

    public function test_order_can_be_created(): void
    {
        $response = $this->actingAs($this->user)->post(route('orders.store'), [
            'company_id' => $this->company->id,
            'customer_id' => $this->customer->id,
            'order_date' => now()->toDateString(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('sales_orders', ['status' => 'draft']);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('orders.store'), []);

        $response->assertSessionHasErrors(['company_id', 'customer_id', 'order_date']);
    }

    public function test_order_can_be_confirmed(): void
    {
        $order = SalesOrder::factory()->create([
            'company_id' => $this->company->id,
            'customer_id' => $this->customer->id,
        ]);

        $response = $this->actingAs($this->user)->patch(route('orders.confirm', $order));

        $response->assertRedirect();
        $this->assertDatabaseHas('sales_orders', ['id' => $order->id, 'status' => 'confirmed']);
    }

    public function test_order_can_be_cancelled(): void
    {
        $order = SalesOrder::factory()->create([
            'company_id' => $this->company->id,
            'customer_id' => $this->customer->id,
        ]);

        $response = $this->actingAs($this->user)->patch(route('orders.cancel', $order));

        $response->assertRedirect();
        $this->assertDatabaseHas('sales_orders', ['id' => $order->id, 'status' => 'cancelled']);
    }
}
