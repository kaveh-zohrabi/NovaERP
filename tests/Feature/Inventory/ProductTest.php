<?php

declare(strict_types=1);

namespace Tests\Feature\Inventory;

use App\Models\Company;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_index_displays_products(): void
    {
        Product::factory()->count(3)->create(['company_id' => Company::factory()->create()->id]);

        $response = $this->actingAs($this->user)->get(route('products.index'));

        $response->assertOk();
        $response->assertSee('Products');
    }

    public function test_index_can_search_products(): void
    {
        Product::factory()->create(['name' => 'Widget Pro', 'company_id' => Company::factory()->create()->id]);
        Product::factory()->create(['name' => 'Gadget Lite', 'company_id' => Company::factory()->create()->id]);

        $response = $this->actingAs($this->user)->get(route('products.index', ['search' => 'Widget']));

        $response->assertOk();
        $response->assertSee('Widget Pro');
        $response->assertDontSee('Gadget Lite');
    }

    public function test_product_can_be_created(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($this->user)->post(route('products.store'), [
            'company_id' => $company->id,
            'name' => 'Widget Pro',
            'slug' => 'widget-pro',
            'sku' => 'WGT-001',
            'cost_price' => 10.00,
            'selling_price' => 25.00,
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', ['sku' => 'WGT-001']);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('products.store'), []);

        $response->assertSessionHasErrors(['company_id', 'name', 'sku', 'cost_price', 'selling_price', 'status']);
    }

    public function test_store_validates_selling_price_gte_cost_price(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($this->user)->post(route('products.store'), [
            'company_id' => $company->id,
            'name' => 'Widget',
            'slug' => 'widget',
            'sku' => 'WGT-001',
            'cost_price' => 100,
            'selling_price' => 50,
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('selling_price');
    }

    public function test_product_can_be_updated(): void
    {
        $product = Product::factory()->create(['company_id' => Company::factory()->create()->id]);

        $response = $this->actingAs($this->user)->put(route('products.update', $product), [
            'name' => 'Updated Product',
            'slug' => $product->slug,
            'sku' => $product->sku,
            'cost_price' => $product->cost_price,
            'selling_price' => $product->selling_price,
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Updated Product']);
    }

    public function test_product_can_be_deleted(): void
    {
        $product = Product::factory()->create(['company_id' => Company::factory()->create()->id]);

        $response = $this->actingAs($this->user)->delete(route('products.destroy', $product));

        $response->assertRedirect();
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }
}
