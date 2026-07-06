<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Position\StorePositionRequest;
use App\Http\Requests\Position\UpdatePositionRequest;
use App\Models\Position;
use App\Services\PositionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PositionController extends Controller
{
    public function __construct(
        private readonly PositionService $positionService,
    ) {}

    /**
     * Display a listing of positions.
     */
    public function index(Request $request): View
    {
        $positions = Position::with('department', 'company')
            ->when($request->search, fn ($query, $search) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('positions.index', compact('positions'));
    }

    /**
     * Show the form for creating a new position.
     */
    public function create(): View
    {
        return view('positions.create');
    }

    /**
     * Store a newly created position.
     */
    public function store(StorePositionRequest $request): RedirectResponse
    {
        $position = $this->positionService->create(
            $request->validated(),
            $request->user(),
        );

        return redirect()
            ->route('positions.show', $position)
            ->with('success', 'Position created successfully.');
    }

    /**
     * Display the specified position.
     */
    public function show(Position $position): View
    {
        $position->load('department', 'company', 'createdBy');

        return view('positions.show', compact('position'));
    }

    /**
     * Show the form for editing the specified position.
     */
    public function edit(Position $position): View
    {
        return view('positions.edit', compact('position'));
    }

    /**
     * Update the specified position.
     */
    public function update(UpdatePositionRequest $request, Position $position): RedirectResponse
    {
        $position = $this->positionService->update($position, $request->validated());

        return redirect()
            ->route('positions.show', $position)
            ->with('success', 'Position updated successfully.');
    }

    /**
     * Soft delete the specified position.
     */
    public function destroy(Position $position): RedirectResponse
    {
        $result = $this->positionService->delete($position);

        return back()->with('success', $result['message']);
    }

    /**
     * Restore a soft-deleted position.
     */
    public function restore(int $id): RedirectResponse
    {
        $position = Position::withTrashed()->findOrFail($id);

        $this->positionService->restore($position);

        return back()->with('success', 'Position restored.');
    }

    /**
     * Activate the specified position.
     */
    public function activate(Position $position): RedirectResponse
    {
        $this->positionService->activate($position);

        return back()->with('success', 'Position activated.');
    }

    /**
     * Deactivate the specified position.
     */
    public function deactivate(Position $position): RedirectResponse
    {
        $this->positionService->deactivate($position);

        return back()->with('success', 'Position deactivated.');
    }
}
