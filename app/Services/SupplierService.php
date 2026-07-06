<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Supplier;
use App\Models\User;
use App\Support\BaseService;

class SupplierService extends BaseService
{
    public function create(array $data, User $creator): Supplier
    {
        return Supplier::create(
            array_merge($data, ['created_by' => $creator->id])
        );
    }

    public function update(Supplier $supplier, array $data): Supplier
    {
        $data['updated_by'] = auth()->id();

        $supplier->update($data);

        return $supplier->fresh();
    }

    public function delete(Supplier $supplier): array
    {
        $supplier->delete();

        return [
            'success' => true,
            'message' => 'Supplier deleted successfully.',
        ];
    }

    public function restore(Supplier $supplier): bool
    {
        if (! $supplier->trashed()) {
            return false;
        }

        $supplier->restore();

        return true;
    }
}
