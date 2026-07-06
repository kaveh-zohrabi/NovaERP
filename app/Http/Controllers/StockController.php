<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockController extends Controller
{
    public function __construct(
        private readonly StockService $stockService,
    ) {}

    public function index(Request $request): View
    {
        $stock = Stock::with('product', 'warehouse')
            ->when($request->search, fn ($query, $search) => $query
                ->whereHas('product', fn ($q) => $q->where('name', 'like', "%{$search}%"))
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('inventory.stock.index', compact('stock'));
    }

    public function show(Stock $stock): View
    {
        $stock->load('product', 'warehouse');

        return view('inventory.stock.show', compact('stock'));
    }
}
