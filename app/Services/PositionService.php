<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Position;
use App\Models\User;
use App\Support\BaseService;

class PositionService extends BaseService
{
    /**
     * Create a new position.
     */
    public function create(array $data, User $creator): Position
    {
        return Position::create(
            array_merge(
                $data,
                ['created_by' => $creator->id],
            )
        );
    }

    /**
     * Update a position.
     */
    public function update(Position $position, array $data): Position
    {
        $data['updated_by'] = auth()->id();

        $position->update($data);

        return $position->fresh();
    }

    /**
     * Activate a position.
     */
    public function activate(Position $position): bool
    {
        if ($position->isActive()) {
            return false;
        }

        $position->update(['status' => 'active']);

        return true;
    }

    /**
     * Deactivate a position.
     */
    public function deactivate(Position $position): bool
    {
        if (! $position->isActive()) {
            return false;
        }

        $position->update(['status' => 'inactive']);

        return true;
    }

    /**
     * Soft delete a position.
     */
    public function delete(Position $position): array
    {
        $position->delete();

        return [
            'success' => true,
            'message' => 'Position deleted successfully.',
        ];
    }

    /**
     * Restore a soft-deleted position.
     */
    public function restore(Position $position): bool
    {
        if (! $position->trashed()) {
            return false;
        }

        $position->restore();

        return true;
    }
}
