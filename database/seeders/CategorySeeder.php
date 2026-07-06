<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Company;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();

        if (! $company) {
            $this->command->error('No company found. Run CompanySeeder first.');

            return;
        }

        $categories = ['Electronics', 'Furniture', 'Clothing', 'Food', 'Office Supplies'];

        foreach ($categories as $name) {
            Category::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($name), 'company_id' => $company->id],
                [
                    'name' => $name,
                    'description' => $name.' category',
                    'status' => 'active',
                ]
            );
        }
    }
}
