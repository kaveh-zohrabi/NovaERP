<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Services\EmployeeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function __construct(
        private readonly EmployeeService $employeeService,
    ) {}

    /**
     * Display a listing of employees.
     */
    public function index(Request $request): View
    {
        $employees = Employee::with('branch', 'department', 'position')
            ->when($request->search, fn ($query, $search) => $query
                ->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('employee_code', 'like', "%{$search}%")
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create(): View
    {
        return view('employees.create');
    }

    /**
     * Store a newly created employee.
     */
    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $employee = $this->employeeService->create(
            $request->validated(),
            $request->user(),
        );

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee): View
    {
        $employee->load('company', 'branch', 'department', 'position', 'user', 'createdBy');

        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee): View
    {
        return view('employees.edit', compact('employee'));
    }

    /**
     * Update the specified employee.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $employee = $this->employeeService->update($employee, $request->validated());

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Soft delete the specified employee.
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        $result = $this->employeeService->delete($employee);

        return back()->with('success', $result['message']);
    }

    /**
     * Restore a soft-deleted employee.
     */
    public function restore(int $id): RedirectResponse
    {
        $employee = Employee::withTrashed()->findOrFail($id);

        $this->employeeService->restore($employee);

        return back()->with('success', 'Employee restored.');
    }

    /**
     * Terminate an employee.
     */
    public function terminate(Employee $employee): RedirectResponse
    {
        $this->employeeService->terminate($employee);

        return back()->with('success', 'Employee terminated.');
    }

    /**
     * Reactivate a terminated employee.
     */
    public function reactivate(Employee $employee): RedirectResponse
    {
        $this->employeeService->reactivate($employee);

        return back()->with('success', 'Employee reactivated.');
    }
}
