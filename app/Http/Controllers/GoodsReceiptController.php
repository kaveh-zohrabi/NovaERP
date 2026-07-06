<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Services\GoodsReceiptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GoodsReceiptController extends Controller
{
    public function __construct(
        private readonly GoodsReceiptService $receiptService,
    ) {}

    public function create(PurchaseOrder $order): View
    {
        $order->load('items.product');

        return view('purchasing.receipts.create', compact('order'));
    }

    public function store(Request $request, PurchaseOrder $order): RedirectResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.purchase_order_item_id' => ['required', 'exists:purchase_order_items,id'],
            'items.*.quantity_received' => ['required', 'numeric', 'min:0.01'],
        ]);

        $this->receiptService->create($order, $validated['items'], $request->user());

        return redirect()->route('orders.show', $order)->with('success', 'Goods received successfully.');
    }
}
