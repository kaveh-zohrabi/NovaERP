<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SalesOrder\StoreSalesOrderRequest;
use App\Models\SalesOrder;
use App\Services\SalesOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesOrderController extends Controller
{
    public function __construct(
        private readonly SalesOrderService $orderService,
    ) {}

    public function index(Request $request): View
    {
        $orders = SalesOrder::with('customer')
            ->when($request->search, fn ($query, $search) => $query
                ->where('order_number', 'like', "%{$search}%")
                ->orWhereHas('customer', fn ($q) => $q->where('name', 'like', "%{$search}%"))
            )
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('sales.orders.index', compact('orders'));
    }

    public function create(): View
    {
        return view('sales.orders.create');
    }

    public function store(StoreSalesOrderRequest $request): RedirectResponse
    {
        $order = $this->orderService->create($request->validated(), $request->user());

        return redirect()->route('orders.show', $order)->with('success', 'Sales order created successfully.');
    }

    public function show(SalesOrder $order): View
    {
        $order->load('customer', 'items.product', 'invoices');

        return view('sales.orders.show', compact('order'));
    }

    public function destroy(SalesOrder $order): RedirectResponse
    {
        $result = $this->orderService->delete($order);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function confirm(SalesOrder $order): RedirectResponse
    {
        $this->orderService->confirm($order);

        return back()->with('success', 'Sales order confirmed.');
    }

    public function cancel(SalesOrder $order): RedirectResponse
    {
        $this->orderService->cancel($order);

        return back()->with('success', 'Sales order cancelled.');
    }
}
