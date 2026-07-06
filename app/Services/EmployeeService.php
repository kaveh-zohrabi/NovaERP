<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use App\Support\BaseService;

class EmployeeService extends BaseService
{
    /**
     * Create a new employee.
     */
    public function create(array $data, User $creator): Employee
    {
        return Employee::create(
            array_merge(
                $data,
                ['created_by' => $creator->id],
            )
        );
    }

    /**
     * Update an employee.
     */
    public function update(Employee $employee, array $data): Employee
    {
        $data['updated_by'] = auth()->id();

        $employee->update($data);

        return $employee->fresh();
    }

    /**
     * Assign an employee to a branch, department, and/or position.
     */
    public function assign(Employee $employee, array $assignments): Employee
    {
        $employee->update(array_merge(
            $assignments,
            ['updated_by' => auth()->id()],
        ));

        return $employee->fresh();
    }

    /**
     * Terminate an employee.
     */
    public function terminate(Employee $employee, ?string $date = null): Employee
    {
        $employee->update([
            'status' => 'terminated',
            'termination_date' => $date ?? now()->toDateString(),
            'updated_by' => auth()->id(),
        ]);

        return $employee->fresh();
    }

    /**
     * Reactivate a terminated employee.
     */
    public function reactivate(Employee $employee): Employee
    {
        $employee->update([
            'status' => 'active',
            'termination_date' => null,
            'updated_by' => auth()->id(),
        ]);

        return $employee->fresh();
    }

    /**
     * Soft delete an employee.
     */
    public function delete(Employee $employee): array
    {
        $employee->delete();

        return [
            'success' => true,
            'message' => 'Employee deleted successfully.',
        ];
    }

    /**
     * Restore a soft-deleted employee.
     */
    public function restore(Employee $employee): bool
    {
        if (! $employee->trashed()) {
            return false;
        }

        $employee->restore();

        return true;
    }
}
