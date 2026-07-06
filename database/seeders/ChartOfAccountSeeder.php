<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use App\Models\Company;
use Illuminate\Database\Seeder;

class ChartOfAccountSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();

        if (! $company) {
            $this->command->error('No company found. Run CompanySeeder first.');

            return;
        }

        $accounts = [
            // Assets
            ['code' => '1000', 'name' => 'Cash', 'type' => 'asset'],
            ['code' => '1100', 'name' => 'Accounts Receivable', 'type' => 'asset'],
            ['code' => '1200', 'name' => 'Inventory', 'type' => 'asset'],
            ['code' => '1300', 'name' => 'Prepaid Expenses', 'type' => 'asset'],

            // Liabilities
            ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'liability'],
            ['code' => '2100', 'name' => 'Sales Tax Payable', 'type' => 'liability'],
            ['code' => '2200', 'name' => 'Accrued Expenses', 'type' => 'liability'],

            // Equity
            ['code' => '3000', 'name' => 'Owner\'s Equity', 'type' => 'equity'],
            ['code' => '3100', 'name' => 'Retained Earnings', 'type' => 'equity'],

            // Revenue
            ['code' => '4000', 'name' => 'Sales Revenue', 'type' => 'revenue'],
            ['code' => '4100', 'name' => 'Service Revenue', 'type' => 'revenue'],
            ['code' => '4200', 'name' => 'Other Income', 'type' => 'revenue'],

            // Expense
            ['code' => '5000', 'name' => 'Cost of Goods Sold', 'type' => 'expense'],
            ['code' => '5100', 'name' => 'Salaries Expense', 'type' => 'expense'],
            ['code' => '5200', 'name' => 'Rent Expense', 'type' => 'expense'],
            ['code' => '5300', 'name' => 'Utilities Expense', 'type' => 'expense'],
            ['code' => '5400', 'name' => 'Office Supplies', 'type' => 'expense'],
        ];

        foreach ($accounts as $account) {
            ChartOfAccount::updateOrCreate(
                ['code' => $account['code'], 'company_id' => $company->id],
                [
                    'name' => $account['name'],
                    'type' => $account['type'],
                    'is_active' => true,
                ]
            );
        }
    }
}
