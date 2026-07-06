<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Branch\StoreBranchRequest;
use App\Http\Requests\Branch\UpdateBranchRequest;
use App\Models\Branch;
use App\Services\BranchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function __construct(
        private readonly BranchService $branchService,
    ) {}

    /**
     * Display a listing of branches.
     */
    public function index(Request $request): View
    {
        $branches = Branch::with('company')
            ->when($request->search, fn ($query, $search) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new branch.
     */
    public function create(): View
    {
        return view('branches.create');
    }

    /**
     * Store a newly created branch.
     */
    public function store(StoreBranchRequest $request): RedirectResponse
    {
        $branch = $this->branchService->create(
            $request->validated(),
            $request->user(),
        );

        return redirect()
            ->route('branches.show', $branch)
            ->with('success', 'Branch created successfully.');
    }

    /**
     * Display the specified branch.
     */
    public function show(Branch $branch): View
    {
        $branch->load('company', 'createdBy');

        return view('branches.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified branch.
     */
    public function edit(Branch $branch): View
    {
        return view('branches.edit', compact('branch'));
    }

    /**
     * Update the specified branch.
     */
    public function update(UpdateBranchRequest $request, Branch $branch): RedirectResponse
    {
        $branch = $this->branchService->update($branch, $request->validated());

        return redirect()
            ->route('branches.show', $branch)
            ->with('success', 'Branch updated successfully.');
    }

    /**
     * Soft delete the specified branch.
     */
    public function destroy(Branch $branch): RedirectResponse
    {
        $result = $this->branchService->delete($branch);

        return back()->with('success', $result['message']);
    }

    /**
     * Restore a soft-deleted branch.
     */
    public function restore(int $id): RedirectResponse
    {
        $branch = Branch::withTrashed()->findOrFail($id);

        $this->branchService->restore($branch);

        return back()->with('success', 'Branch restored.');
    }

    /**
     * Activate the specified branch.
     */
    public function activate(Branch $branch): RedirectResponse
    {
        $this->branchService->activate($branch);

        return back()->with('success', 'Branch activated.');
    }

    /**
     * Deactivate the specified branch.
     */
    public function deactivate(Branch $branch): RedirectResponse
    {
        $this->branchService->deactivate($branch);

        return back()->with('success', 'Branch deactivated.');
    }
}
