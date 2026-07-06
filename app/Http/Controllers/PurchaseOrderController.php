<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseOrder\StorePurchaseOrderRequest;
use App\Http\Requests\PurchaseOrder\UpdatePurchaseOrderRequest;
use App\Models\PurchaseOrder;
use App\Services\PurchaseOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    public function __construct(
        private readonly PurchaseOrderService $orderService,
    ) {}

    public function index(Request $request): View
    {
        $orders = PurchaseOrder::with('supplier', 'warehouse')
            ->when($request->search, fn ($query, $search) => $query
                ->where('order_number', 'like', "%{$search}%")
                ->orWhereHas('supplier', fn ($q) => $q->where('name', 'like', "%{$search}%"))
            )
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('purchasing.orders.index', compact('orders'));
    }

    public function create(): View
    {
        return view('purchasing.orders.create');
    }

    public function store(StorePurchaseOrderRequest $request): RedirectResponse
    {
        $order = $this->orderService->create($request->validated(), $request->user());

        return redirect()->route('purchasing-orders.show', $order)->with('success', 'Purchase order created successfully.');
    }

    public function show(PurchaseOrder $purchasingOrder): View
    {
        $purchasingOrder->load('supplier', 'warehouse', 'items.product', 'goodsReceipts');

        return view('purchasing.orders.show', ['order' => $purchasingOrder]);
    }

    public function edit(PurchaseOrder $purchasingOrder): View
    {
        return view('purchasing.orders.edit', ['order' => $purchasingOrder]);
    }

    public function update(UpdatePurchaseOrderRequest $request, PurchaseOrder $purchasingOrder): RedirectResponse
    {
        $purchasingOrder->update($request->validated());

        return redirect()->route('purchasing-orders.show', $purchasingOrder)->with('success', 'Purchase order updated successfully.');
    }

    public function destroy(PurchaseOrder $purchasingOrder): RedirectResponse
    {
        $result = $this->orderService->delete($purchasingOrder);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function approve(PurchaseOrder $purchasingOrder): RedirectResponse
    {
        $this->orderService->approve($purchasingOrder);

        return back()->with('success', 'Purchase order approved.');
    }

    public function cancel(PurchaseOrder $purchasingOrder): RedirectResponse
    {
        $this->orderService->cancel($purchasingOrder);

        return back()->with('success', 'Purchase order cancelled.');
    }
}
