<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::updateOrCreate(
            ['slug' => 'novaerp'],
            [
                'name' => 'NovaERP',
                'legal_name' => 'NovaERP Systems',
                'registration_number' => '123456789',
                'tax_number' => 'US-123456789',
                'email' => 'info@novaerp.com',
                'phone' => '+1 555 123 4567',
                'website' => 'https://novaerp.com',
                'address' => '123 Business Avenue, Suite 500',
                'city' => 'New York',
                'state' => 'NY',
                'country' => 'US',
                'postal_code' => '10001',
                'status' => 'active',
                'settings' => [
                    'currency' => 'USD',
                    'timezone' => 'America/New_York',
                    'date_format' => 'Y-m-d',
                    'fiscal_year_start' => '01-01',
                    'invoice_prefix' => 'INV-',
                ],
            ]
        );
    }
}
