<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Customer;
use App\Models\User;
use App\Support\BaseService;

class CustomerService extends BaseService
{
    public function create(array $data, User $creator): Customer
    {
        return Customer::create(
            array_merge($data, ['created_by' => $creator->id])
        );
    }

    public function update(Customer $customer, array $data): Customer
    {
        $data['updated_by'] = auth()->id();

        $customer->update($data);

        return $customer->fresh();
    }

    public function delete(Customer $customer): array
    {
        $customer->delete();

        return [
            'success' => true,
            'message' => 'Customer deleted successfully.',
        ];
    }

    public function restore(Customer $customer): bool
    {
        if (! $customer->trashed()) {
            return false;
        }

        $customer->restore();

        return true;
    }
}
