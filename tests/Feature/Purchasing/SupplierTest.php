<?php

declare(strict_types=1);

namespace Tests\Feature\Purchasing;

use App\Models\Company;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_index_displays_suppliers(): void
    {
        Supplier::factory()->count(3)->create(['company_id' => Company::factory()->create()->id]);

        $response = $this->actingAs($this->user)->get(route('suppliers.index'));

        $response->assertOk();
        $response->assertSee('Suppliers');
    }

    public function test_supplier_can_be_created(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($this->user)->post(route('suppliers.store'), [
            'company_id' => $company->id,
            'name' => 'Acme Supplies',
            'email' => 'contact@acme.com',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('suppliers', ['name' => 'Acme Supplies']);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('suppliers.store'), []);

        $response->assertSessionHasErrors(['company_id', 'name', 'status']);
    }

    public function test_supplier_can_be_updated(): void
    {
        $supplier = Supplier::factory()->create(['company_id' => Company::factory()->create()->id]);

        $response = $this->actingAs($this->user)->put(route('suppliers.update', $supplier), [
            'name' => 'Updated Supplier',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id, 'name' => 'Updated Supplier']);
    }

    public function test_supplier_can_be_deleted(): void
    {
        $supplier = Supplier::factory()->create(['company_id' => Company::factory()->create()->id]);

        $response = $this->actingAs($this->user)->delete(route('suppliers.destroy', $supplier));

        $response->assertRedirect();
        $this->assertSoftDeleted('suppliers', ['id' => $supplier->id]);
    }
}
