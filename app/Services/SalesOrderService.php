<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\User;
use App\Support\BaseService;

class SalesOrderService extends BaseService
{
    public function create(array $data, User $creator): SalesOrder
    {
        if (empty($data['order_number'])) {
            $data['order_number'] = $this->generateOrderNumber();
        }

        return SalesOrder::create(
            array_merge($data, ['created_by' => $creator->id])
        );
    }

    public function addItem(SalesOrder $order, array $data): SalesOrderItem
    {
        if (! $order->isDraft()) {
            throw new \InvalidArgumentException('Only draft orders can have items added.');
        }

        $data['total_price'] = $data['quantity'] * $data['unit_price'];

        $item = $order->items()->create($data);

        $this->recalculateTotal($order);

        return $item;
    }

    public function removeItem(SalesOrderItem $item): bool
    {
        $order = $item->salesOrder;

        if (! $order->isDraft()) {
            throw new \InvalidArgumentException('Only draft orders can have items removed.');
        }

        $item->delete();

        $this->recalculateTotal($order);

        return true;
    }

    public function confirm(SalesOrder $order): bool
    {
        if (! $order->isDraft()) {
            return false;
        }

        $order->update(['status' => 'confirmed']);

        return true;
    }

    public function cancel(SalesOrder $order): bool
    {
        if ($order->isCancelled()) {
            return false;
        }

        $order->update(['status' => 'cancelled']);

        return true;
    }

    public function recalculateTotal(SalesOrder $order): void
    {
        $total = $order->items()->sum('total_price');

        $order->update(['total_amount' => $total]);
    }

    public function delete(SalesOrder $order): array
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
            'message' => 'Sales order deleted successfully.',
        ];
    }

    public function restore(SalesOrder $order): bool
    {
        if (! $order->trashed()) {
            return false;
        }

        $order->restore();

        return true;
    }

    private function generateOrderNumber(): string
    {
        $year = now()->format('Y');
        $sequence = SalesOrder::whereYear('created_at', now()->year)->count() + 1;

        return "SO-{$year}-".str_pad((string) $sequence, 5, '0', STR_PAD_LEFT);
    }
}
