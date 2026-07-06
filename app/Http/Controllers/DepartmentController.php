<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Department\StoreDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Models\Department;
use App\Services\DepartmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function __construct(
        private readonly DepartmentService $departmentService,
    ) {}

    /**
     * Display a listing of departments.
     */
    public function index(Request $request): View
    {
        $departments = Department::with('branch', 'company')
            ->when($request->search, fn ($query, $search) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new department.
     */
    public function create(): View
    {
        return view('departments.create');
    }

    /**
     * Store a newly created department.
     */
    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        $department = $this->departmentService->create(
            $request->validated(),
            $request->user(),
        );

        return redirect()
            ->route('departments.show', $department)
            ->with('success', 'Department created successfully.');
    }

    /**
     * Display the specified department.
     */
    public function show(Department $department): View
    {
        $department->load('branch', 'company', 'createdBy');

        return view('departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit(Department $department): View
    {
        return view('departments.edit', compact('department'));
    }

    /**
     * Update the specified department.
     */
    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        $department = $this->departmentService->update($department, $request->validated());

        return redirect()
            ->route('departments.show', $department)
            ->with('success', 'Department updated successfully.');
    }

    /**
     * Soft delete the specified department.
     */
    public function destroy(Department $department): RedirectResponse
    {
        $result = $this->departmentService->delete($department);

        return back()->with('success', $result['message']);
    }

    /**
     * Restore a soft-deleted department.
     */
    public function restore(int $id): RedirectResponse
    {
        $department = Department::withTrashed()->findOrFail($id);

        $this->departmentService->restore($department);

        return back()->with('success', 'Department restored.');
    }

    /**
     * Activate the specified department.
     */
    public function activate(Department $department): RedirectResponse
    {
        $this->departmentService->activate($department);

        return back()->with('success', 'Department activated.');
    }

    /**
     * Deactivate the specified department.
     */
    public function deactivate(Department $department): RedirectResponse
    {
        $this->departmentService->deactivate($department);

        return back()->with('success', 'Department deactivated.');
    }
}
