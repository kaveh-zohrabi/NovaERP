<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Contact;
use App\Models\Customer;
use App\Models\Lead;
use App\Support\BaseService;

class CustomerConversionService extends BaseService
{
    public function __construct(
        private readonly LeadService $leadService,
    ) {}

    public function convert(Lead $lead): Customer
    {
        return $this->transaction(function () use ($lead) {
            $customer = $this->leadService->convertToCustomer($lead);

            $contacts = $lead->contacts;
            foreach ($contacts as $contact) {
                $contact->update([
                    'customer_id' => $customer->id,
                    'lead_id' => null,
                ]);
            }

            $lead->opportunities()->update([
                'customer_id' => $customer->id,
            ]);

            return $customer;
        });
    }
}
