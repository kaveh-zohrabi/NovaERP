<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\User;
use App\Support\BaseService;

class StockMovementService extends BaseService
{
    public function __construct(
        private readonly StockService $stockService,
    ) {}

    /**
     * Record a stock IN movement.
     */
    public function stockIn(int $productId, int $warehouseId, float $quantity, ?string $notes, User $user): StockMovement
    {
        $stock = $this->stockService->getOrCreateStock($productId, $warehouseId);

        $stock->update([
            'quantity' => $stock->quantity + $quantity,
            'available_quantity' => $stock->available_quantity + $quantity,
        ]);

        return StockMovement::create([
            'stock_id' => $stock->id,
            'movement_type' => 'IN',
            'quantity' => $quantity,
            'notes' => $notes,
            'performed_by' => $user->id,
        ]);
    }

    /**
     * Record a stock OUT movement.
     */
    public function stockOut(int $productId, int $warehouseId, float $quantity, ?string $notes, User $user): StockMovement
    {
        $stock = $this->stockService->getOrCreateStock($productId, $warehouseId);

        if ($stock->available_quantity < $quantity) {
            throw new \InvalidArgumentException(
                "Insufficient stock. Available: {$stock->available_quantity}, Requested: {$quantity}"
            );
        }

        $stock->update([
            'quantity' => $stock->quantity - $quantity,
            'available_quantity' => $stock->available_quantity - $quantity,
        ]);

        return StockMovement::create([
            'stock_id' => $stock->id,
            'movement_type' => 'OUT',
            'quantity' => $quantity,
            'notes' => $notes,
            'performed_by' => $user->id,
        ]);
    }

    /**
     * Record a stock transfer between warehouses.
     */
    public function transfer(
        int $productId,
        int $fromWarehouseId,
        int $toWarehouseId,
        float $quantity,
        ?string $notes,
        User $user,
    ): array {
        if ($fromWarehouseId === $toWarehouseId) {
            throw new \InvalidArgumentException('Source and destination warehouses must be different.');
        }

        $outMovement = $this->stockOut($productId, $fromWarehouseId, $quantity, $notes, $user);
        $inMovement = $this->stockIn($productId, $toWarehouseId, $quantity, $notes, $user);

        return ['out' => $outMovement, 'in' => $inMovement];
    }

    /**
     * Record a stock adjustment.
     */
    public function adjust(int $productId, int $warehouseId, float $quantity, ?string $notes, User $user): StockMovement
    {
        if (empty($notes)) {
            throw new \InvalidArgumentException('Adjustment requires notes.');
        }

        $stock = $this->stockService->getOrCreateStock($productId, $warehouseId);

        $stock->update([
            'quantity' => $stock->quantity + $quantity,
            'available_quantity' => $stock->available_quantity + $quantity,
        ]);

        return StockMovement::create([
            'stock_id' => $stock->id,
            'movement_type' => 'ADJUSTMENT',
            'quantity' => $quantity,
            'notes' => $notes,
            'performed_by' => $user->id,
        ]);
    }

    /**
     * Get movement history for a stock record.
     */
    public function getMovements(int $stockId)
    {
        return StockMovement::where('stock_id', $stockId)
            ->with('performedBy')
            ->orderByDesc('created_at')
            ->paginate(15);
    }
}
