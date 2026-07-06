<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\SalesOrder;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function __construct(
        private readonly InvoiceService $invoiceService,
    ) {}

    public function index(Request $request): View
    {
        $invoices = Invoice::with('salesOrder.customer')
            ->when($request->search, fn ($query, $search) => $query
                ->where('invoice_number', 'like', "%{$search}%")
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('sales.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load('salesOrder.customer', 'items.product');

        return view('sales.invoices.show', compact('invoice'));
    }

    public function generate(SalesOrder $order): RedirectResponse
    {
        $this->invoiceService->createFromOrder($order);

        return back()->with('success', 'Invoice generated successfully.');
    }

    public function markPaid(Invoice $invoice): RedirectResponse
    {
        $this->invoiceService->markPaid($invoice);

        return back()->with('success', 'Invoice marked as paid.');
    }

    public function markCancelled(Invoice $invoice): RedirectResponse
    {
        $this->invoiceService->markCancelled($invoice);

        return back()->with('success', 'Invoice cancelled.');
    }
}
