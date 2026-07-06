<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Invoice;
use App\Models\SalesOrder;
use App\Support\BaseService;

class InvoiceService extends BaseService
{
    public function createFromOrder(SalesOrder $order): Invoice
    {
        if ($order->isCancelled()) {
            throw new \InvalidArgumentException('Cannot invoice a cancelled order.');
        }

        if ($order->invoices()->where('status', '!=', 'cancelled')->exists()) {
            throw new \InvalidArgumentException('This order already has an active invoice.');
        }

        return $this->transaction(function () use ($order) {
            $subtotal = $order->total_amount;
            $taxRate = 0.10;
            $taxAmount = round($subtotal * $taxRate, 2);
            $discountAmount = 0;
            $totalAmount = $subtotal + $taxAmount - $discountAmount;

            $invoice = Invoice::create([
                'sales_order_id' => $order->id,
                'invoice_number' => $this->generateInvoiceNumber(),
                'status' => 'draft',
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'issued_at' => now(),
            ]);

            foreach ($order->items as $item) {
                $invoice->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                ]);
            }

            $order->update(['status' => 'invoiced']);

            return $invoice;
        });
    }

    public function markPaid(Invoice $invoice): Invoice
    {
        $invoice->update(['status' => 'paid']);

        return $invoice->fresh();
    }

    public function markCancelled(Invoice $invoice): Invoice
    {
        $invoice->update(['status' => 'cancelled']);

        return $invoice->fresh();
    }

    private function generateInvoiceNumber(): string
    {
        $year = now()->format('Y');
        $sequence = Invoice::whereYear('created_at', now()->year)->count() + 1;

        return "INV-{$year}-".str_pad((string) $sequence, 5, '0', STR_PAD_LEFT);
    }
}
