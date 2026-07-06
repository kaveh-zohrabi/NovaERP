<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Stock;
use App\Models\StockMovement;
use App\Support\BaseService;

class StockService extends BaseService
{
    /**
     * Get stock for a product in a warehouse.
     */
    public function getStock(int $productId, int $warehouseId): ?Stock
    {
        return Stock::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->first();
    }

    /**
     * Get or create stock record.
     */
    public function getOrCreateStock(int $productId, int $warehouseId): Stock
    {
        return Stock::firstOrCreate(
            ['product_id' => $productId, 'warehouse_id' => $warehouseId],
            ['quantity' => 0, 'reserved_quantity' => 0, 'available_quantity' => 0]
        );
    }

    /**
     * Get all stock for a product across warehouses.
     */
    public function getProductStock(int $productId)
    {
        return Stock::where('product_id', $productId)
            ->with('warehouse')
            ->get();
    }

    /**
     * Get total stock for a product across all warehouses.
     */
    public function getTotalStock(int $productId): float
    {
        return (float) Stock::where('product_id', $productId)
            ->sum('quantity');
    }

    /**
     * Get products below reorder level.
     */
    public function getLowStockProducts(int $companyId)
    {
        return Stock::whereHas('product', fn ($q) => $q->where('company_id', $companyId))
            ->whereColumn('available_quantity', '<=', 'reorder_level')
            ->with('product', 'warehouse')
            ->get();
    }
}
