<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Branch;
use App\Models\User;
use App\Support\BaseService;

class BranchService extends BaseService
{
    /**
     * Create a new branch.
     */
    public function create(array $data, User $creator): Branch
    {
        return Branch::create(
            array_merge(
                $data,
                ['created_by' => $creator->id],
            )
        );
    }

    /**
     * Update a branch.
     */
    public function update(Branch $branch, array $data): Branch
    {
        $data['updated_by'] = auth()->id();

        $branch->update($data);

        return $branch->fresh();
    }

    /**
     * Activate a branch.
     */
    public function activate(Branch $branch): bool
    {
        if ($branch->isActive()) {
            return false;
        }

        $branch->update(['status' => 'active']);

        return true;
    }

    /**
     * Deactivate a branch.
     */
    public function deactivate(Branch $branch): bool
    {
        if (! $branch->isActive()) {
            return false;
        }

        $branch->update(['status' => 'inactive']);

        return true;
    }

    /**
     * Soft delete a branch.
     */
    public function delete(Branch $branch): array
    {
        $branch->delete();

        return [
            'success' => true,
            'message' => 'Branch deleted successfully.',
        ];
    }

    /**
     * Restore a soft-deleted branch.
     */
    public function restore(Branch $branch): bool
    {
        if (! $branch->trashed()) {
            return false;
        }

        $branch->restore();

        return true;
    }
}
