<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Support\BaseService;

class InventoryReportService extends BaseService
{
    public function getValuation(array $filters = []): array
    {
        $companyId = $filters['company_id'] ?? 1;

        $stockData = Stock::where('company_id', $companyId)
            ->join('products', 'stock.product_id', '=', 'products.id')
            ->join('warehouses', 'stock.warehouse_id', '=', 'warehouses.id')
            ->selectRaw('
                products.id as product_id,
                products.name as product_name,
                products.sku,
                products.cost_price,
                warehouses.name as warehouse_name,
                stock.quantity,
                stock.quantity * products.cost_price as total_value
            ')
            ->orderBy('products.name')
            ->get();

        return [
            'total_value' => (float) $stockData->sum('total_value'),
            'items' => $stockData->toArray(),
        ];
    }

    public function getLowStock(array $filters = []): array
    {
        $companyId = $filters['company_id'] ?? 1;

        $lowStock = Stock::where('company_id', $companyId)
            ->where('reorder_level', '>', 0)
            ->whereColumn('quantity', '<=', 'reorder_level')
            ->join('products', 'stock.product_id', '=', 'products.id')
            ->join('warehouses', 'stock.warehouse_id', '=', 'warehouses.id')
            ->selectRaw('
                products.id as product_id,
                products.name as product_name,
                products.sku,
                warehouses.name as warehouse_name,
                stock.quantity,
                stock.reorder_level,
                stock.reorder_level - stock.quantity as deficit
            ')
            ->orderBy('deficit', 'desc')
            ->get();

        return [
            'count' => $lowStock->count(),
            'items' => $lowStock->toArray(),
        ];
    }

    public function getMovements(array $filters = []): array
    {
        $companyId = $filters['company_id'] ?? 1;
        $startDate = $filters['start_date'] ?? now()->subDays(30)->toDateString();
        $endDate = $filters['end_date'] ?? now()->toDateString();

        $movements = StockMovement::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('product', 'warehouse')
            ->latest()
            ->get();

        $summary = $movements->groupBy('type')->map(fn ($group) => [
            'count' => $group->count(),
            'total_quantity' => $group->sum('quantity'),
        ])->toArray();

        return [
            'period' => ['start' => $startDate, 'end' => $endDate],
            'total_movements' => $movements->count(),
            'summary' => $summary,
            'movements' => $movements->toArray(),
        ];
    }

    public function getFastMoving(array $filters = []): array
    {
        $companyId = $filters['company_id'] ?? 1;
        $days = $filters['days'] ?? 30;

        $since = now()->subDays($days);

        $fastMoving = StockMovement::where('company_id', $companyId)
            ->where('type', 'OUT')
            ->where('created_at', '>=', $since)
            ->join('products', 'stock_movements.product_id', '=', 'products.id')
            ->selectRaw('products.id, products.name, products.sku, SUM(stock_movements.quantity) as total_out')
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_out')
            ->limit(20)
            ->get();

        return [
            'period_days' => $days,
            'products' => $fastMoving->toArray(),
        ];
    }
}
