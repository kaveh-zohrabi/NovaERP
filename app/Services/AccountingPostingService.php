<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Support\BaseService;

class AccountingPostingService extends BaseService
{
    public function __construct(
        private readonly JournalEntryService $journalEntryService,
    ) {}

    /**
     * Post invoice to accounting.
     *
     * DR Accounts Receivable
     * CR Revenue
     */
    public function postInvoice(Invoice $invoice, User $postedBy): void
    {
        $arAccount = ChartOfAccount::where('company_id', $invoice->salesOrder->company_id)
            ->where('code', '1200')
            ->firstOrFail();

        $revenueAccount = ChartOfAccount::where('company_id', $invoice->salesOrder->company_id)
            ->where('code', '4000')
            ->firstOrFail();

        $this->journalEntryService->create(
            [
                'company_id' => $invoice->salesOrder->company_id,
                'date' => $invoice->issued_at ?? now()->toDateString(),
                'description' => 'Invoice '.$invoice->invoice_number,
                'reference_type' => Invoice::class,
                'reference_id' => $invoice->id,
            ],
            [
                ['account_id' => $arAccount->id, 'debit' => $invoice->total_amount, 'credit' => 0],
                ['account_id' => $revenueAccount->id, 'debit' => 0, 'credit' => $invoice->total_amount],
            ],
            $postedBy
        );
    }

    /**
     * Post purchase to accounting.
     *
     * DR Inventory/Expense
     * CR Accounts Payable
     */
    public function postPurchase(PurchaseOrder $order, User $postedBy): void
    {
        $inventoryAccount = ChartOfAccount::where('company_id', $order->company_id)
            ->where('code', '1300')
            ->firstOrFail();

        $apAccount = ChartOfAccount::where('company_id', $order->company_id)
            ->where('code', '2000')
            ->firstOrFail();

        $this->journalEntryService->create(
            [
                'company_id' => $order->company_id,
                'date' => now()->toDateString(),
                'description' => 'Purchase Order '.$order->order_number,
                'reference_type' => PurchaseOrder::class,
                'reference_id' => $order->id,
            ],
            [
                ['account_id' => $inventoryAccount->id, 'debit' => $order->total_amount, 'credit' => 0],
                ['account_id' => $apAccount->id, 'debit' => 0, 'credit' => $order->total_amount],
            ],
            $postedBy
        );
    }
}
