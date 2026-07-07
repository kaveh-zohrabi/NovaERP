<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Customer;
use App\Models\Lead;
use App\Support\BaseService;

class LeadService extends BaseService
{
    public function create(array $data): Lead
    {
        return Lead::create($data);
    }

    public function update(Lead $lead, array $data): Lead
    {
        $lead->update($data);

        return $lead->fresh();
    }

    public function delete(Lead $lead): array
    {
        $lead->delete();

        return ['success' => true, 'message' => 'Lead deleted successfully.'];
    }

    public function convertToCustomer(Lead $lead): Customer
    {
        if ($lead->isConverted()) {
            throw new \InvalidArgumentException('This lead has already been converted.');
        }

        return $this->transaction(function () use ($lead) {
            $customer = Customer::create([
                'company_id' => $lead->company_id,
                'name' => $lead->fullName(),
                'email' => $lead->email,
                'phone' => $lead->phone,
                'address' => $lead->company_name,
                'status' => 'active',
            ]);

            $lead->update([
                'converted_at' => now(),
                'converted_customer_id' => $customer->id,
            ]);

            return $customer;
        });
    }

    public function markLost(Lead $lead, string $reason): Lead
    {
        $lead->update([
            'status' => 'lost',
            'lost_reason' => $reason,
        ]);

        return $lead->fresh();
    }
}
