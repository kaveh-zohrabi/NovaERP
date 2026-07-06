<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StockMovement\StoreStockMovementRequest;
use App\Models\StockMovement;
use App\Services\StockMovementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    public function __construct(
        private readonly StockMovementService $movementService,
    ) {}

    public function index(Request $request): View
    {
        $movements = StockMovement::with('stock.product', 'stock.warehouse', 'performedBy')
            ->when($request->search, fn ($query, $search) => $query
                ->whereHas('stock.product', fn ($q) => $q->where('name', 'like', "%{$search}%"))
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('inventory.movements.index', compact('movements'));
    }

    public function create(): View
    {
        return view('inventory.movements.create');
    }

    public function store(StoreStockMovementRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        match ($data['movement_type']) {
            'IN' => $this->movementService->stockIn(
                $data['product_id'], $data['warehouse_id'], $data['quantity'], $data['notes'] ?? null, $user
            ),
            'OUT' => $this->movementService->stockOut(
                $data['product_id'], $data['warehouse_id'], $data['quantity'], $data['notes'] ?? null, $user
            ),
            'TRANSFER' => $this->movementService->transfer(
                $data['product_id'], $data['from_warehouse_id'], $data['to_warehouse_id'],
                $data['quantity'], $data['notes'] ?? null, $user
            ),
            'ADJUSTMENT' => $this->movementService->adjust(
                $data['product_id'], $data['warehouse_id'], $data['quantity'], $data['notes'] ?? '', $user
            ),
        };

        return back()->with('success', 'Stock movement recorded successfully.');
    }
}
