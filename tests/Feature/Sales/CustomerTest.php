<?php

declare(strict_types=1);

namespace Tests\Feature\Sales;

use App\Models\Company;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_index_displays_customers(): void
    {
        Customer::factory()->count(3)->create(['company_id' => Company::factory()->create()->id]);

        $response = $this->actingAs($this->user)->get(route('customers.index'));

        $response->assertOk();
        $response->assertSee('Customers');
    }

    public function test_customer_can_be_created(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($this->user)->post(route('customers.store'), [
            'company_id' => $company->id,
            'name' => 'Acme Corp',
            'email' => 'contact@acme.com',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('customers', ['name' => 'Acme Corp']);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('customers.store'), []);

        $response->assertSessionHasErrors(['company_id', 'name', 'status']);
    }

    public function test_customer_can_be_updated(): void
    {
        $customer = Customer::factory()->create(['company_id' => Company::factory()->create()->id]);

        $response = $this->actingAs($this->user)->put(route('customers.update', $customer), [
            'name' => 'Updated Customer',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'name' => 'Updated Customer']);
    }

    public function test_customer_can_be_deleted(): void
    {
        $customer = Customer::factory()->create(['company_id' => Company::factory()->create()->id]);

        $response = $this->actingAs($this->user)->delete(route('customers.destroy', $customer));

        $response->assertRedirect();
        $this->assertSoftDeleted('customers', ['id' => $customer->id]);
    }
}
