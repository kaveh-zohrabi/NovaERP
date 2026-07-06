<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Support\BaseService;

class GoodsReceiptService extends BaseService
{
    public function __construct(
        private readonly StockMovementService $stockMovementService,
    ) {}

    public function create(PurchaseOrder $order, array $items, User $receiver): GoodsReceipt
    {
        if (! $order->isApproved()) {
            throw new \InvalidArgumentException('Only approved orders can receive goods.');
        }

        $receipt = $this->transaction(function () use ($order, $items, $receiver) {
            $receipt = GoodsReceipt::create([
                'purchase_order_id' => $order->id,
                'warehouse_id' => $order->warehouse_id,
                'receipt_number' => $this->generateReceiptNumber(),
                'received_by' => $receiver->id,
                'received_at' => now(),
            ]);

            foreach ($items as $itemData) {
                $orderItem = $order->items()->find($itemData['purchase_order_item_id']);

                if (! $orderItem) {
                    throw new \InvalidArgumentException('Purchase order item not found.');
                }

                if ($itemData['quantity_received'] > $orderItem->quantity) {
                    throw new \InvalidArgumentException(
                        "Cannot receive more than ordered. Ordered: {$orderItem->quantity}, Received: {$itemData['quantity_received']}"
                    );
                }

                $receipt->items()->create([
                    'purchase_order_item_id' => $orderItem->id,
                    'product_id' => $orderItem->product_id,
                    'quantity_received' => $itemData['quantity_received'],
                ]);

                // Create StockMovement (IN)
                $this->stockMovementService->stockIn(
                    $orderItem->product_id,
                    $order->warehouse_id,
                    $itemData['quantity_received'],
                    "PO-{$order->order_number} receipt",
                    $receiver
                );
            }

            // Update order status
            $this->updateOrderStatus($order);

            return $receipt;
        });

        return $receipt;
    }

    private function updateOrderStatus(PurchaseOrder $order): void
    {
        $totalOrdered = $order->items()->sum('quantity');
        $totalReceived = $order->goodsReceipts()
            ->join('goods_receipt_items', 'goods_receipts.id', '=', 'goods_receipt_items.goods_receipt_id')
            ->sum('goods_receipt_items.quantity_received');

        if ($totalReceived >= $totalOrdered) {
            $order->update(['status' => 'completed']);
        } else {
            $order->update(['status' => 'partially_received']);
        }
    }

    private function generateReceiptNumber(): string
    {
        $year = now()->format('Y');
        $sequence = GoodsReceipt::whereYear('created_at', now()->year)->count() + 1;

        return "GR-{$year}-".str_pad((string) $sequence, 5, '0', STR_PAD_LEFT);
    }
}
