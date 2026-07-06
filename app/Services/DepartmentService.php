<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Department;
use App\Models\User;
use App\Support\BaseService;

class DepartmentService extends BaseService
{
    /**
     * Create a new department.
     */
    public function create(array $data, User $creator): Department
    {
        return Department::create(
            array_merge(
                $data,
                ['created_by' => $creator->id],
            )
        );
    }

    /**
     * Update a department.
     */
    public function update(Department $department, array $data): Department
    {
        $data['updated_by'] = auth()->id();

        $department->update($data);

        return $department->fresh();
    }

    /**
     * Activate a department.
     */
    public function activate(Department $department): bool
    {
        if ($department->isActive()) {
            return false;
        }

        $department->update(['status' => 'active']);

        return true;
    }

    /**
     * Deactivate a department.
     */
    public function deactivate(Department $department): bool
    {
        if (! $department->isActive()) {
            return false;
        }

        $department->update(['status' => 'inactive']);

        return true;
    }

    /**
     * Soft delete a department.
     */
    public function delete(Department $department): array
    {
        $department->delete();

        return [
            'success' => true,
            'message' => 'Department deleted successfully.',
        ];
    }

    /**
     * Restore a soft-deleted department.
     */
    public function restore(Department $department): bool
    {
        if (! $department->trashed()) {
            return false;
        }

        $department->restore();

        return true;
    }
}
