<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\User;
use App\Support\BaseService;

class PurchaseOrderService extends BaseService
{
    public function create(array $data, User $creator): PurchaseOrder
    {
        return PurchaseOrder::create(
            array_merge($data, ['created_by' => $creator->id])
        );
    }

    public function addItem(PurchaseOrder $order, array $data): \App\Models\PurchaseOrderItem
    {
        if (! $order->isDraft()) {
            throw new \InvalidArgumentException('Only draft orders can have items added.');
        }

        $data['total_price'] = $data['quantity'] * $data['unit_price'];

        $item = $order->items()->create($data);

        $this->recalculateTotal($order);

        return $item;
    }

    public function removeItem(\App\Models\PurchaseOrderItem $item): bool
    {
        $order = $item->purchaseOrder;

        if (! $order->isDraft()) {
            throw new \InvalidArgumentException('Only draft orders can have items removed.');
        }

        $item->delete();

        $this->recalculateTotal($order);

        return true;
    }

    public function approve(PurchaseOrder $order): bool
    {
        if (! $order->isDraft()) {
            return false;
        }

        $order->update(['status' => 'approved']);

        return true;
    }

    public function cancel(PurchaseOrder $order): bool
    {
        if ($order->isCancelled()) {
            return false;
        }

        $order->update(['status' => 'cancelled']);

        return true;
    }

    public function recalculateTotal(PurchaseOrder $order): void
    {
        $total = $order->items()->sum('total_price');

        $order->update(['total_amount' => $total]);
    }

    public function delete(PurchaseOrder $order): array
    {
        if (! $order->isDraft()) {
            return [
                'success' => false,
                'message' => 'Only draft orders can be deleted.',
            ];
        }

        $order->items()->delete();
        $order->delete();

        return [
            'success' => true,
            'message' => 'Purchase order deleted successfully.',
        ];
    }

    public function restore(PurchaseOrder $order): bool
    {
        if (! $order->trashed()) {
            return false;
        }

        $order->restore();

        return true;
    }
}
