<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Company;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    private StockService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(StockService::class);
    }

    public function test_get_stock_returns_stock_record(): void
    {
        $company = Company::factory()->create();
        $product = Product::factory()->create(['company_id' => $company->id]);
        $warehouse = Warehouse::factory()->create(['company_id' => $company->id]);

        Stock::create([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 50,
            'reserved_quantity' => 0,
            'available_quantity' => 50,
            'reorder_level' => 10,
        ]);

        $stock = $this->service->getStock($product->id, $warehouse->id);

        $this->assertNotNull($stock);
        $this->assertEquals(50, $stock->quantity);
    }

    public function test_get_stock_returns_null_when_not_found(): void
    {
        $stock = $this->service->getStock(999, 999);

        $this->assertNull($stock);
    }

    public function test_get_total_stock(): void
    {
        $company = Company::factory()->create();
        $product = Product::factory()->create(['company_id' => $company->id]);
        $warehouse1 = Warehouse::factory()->create(['company_id' => $company->id]);
        $warehouse2 = Warehouse::factory()->create(['company_id' => $company->id]);

        Stock::create(['product_id' => $product->id, 'warehouse_id' => $warehouse1->id, 'quantity' => 50, 'reserved_quantity' => 0, 'available_quantity' => 50, 'reorder_level' => 0]);
        Stock::create(['product_id' => $product->id, 'warehouse_id' => $warehouse2->id, 'quantity' => 30, 'reserved_quantity' => 0, 'available_quantity' => 30, 'reorder_level' => 0]);

        $total = $this->service->getTotalStock($product->id);

        $this->assertEquals(80, $total);
    }
}
