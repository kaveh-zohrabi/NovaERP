<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use Illuminate\Database\Seeder;

class PipelineSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();

        if (! $company) {
            $this->command->error('No company found. Run CompanySeeder first.');

            return;
        }

        $pipeline = Pipeline::updateOrCreate(
            ['company_id' => $company->id, 'name' => 'Sales Pipeline'],
            [
                'description' => 'Default sales pipeline for tracking opportunities',
                'is_active' => true,
            ]
        );

        $stages = [
            ['name' => 'Qualification', 'probability' => 10, 'sort_order' => 1],
            ['name' => 'Needs Analysis', 'probability' => 20, 'sort_order' => 2],
            ['name' => 'Proposal', 'probability' => 50, 'sort_order' => 3],
            ['name' => 'Negotiation', 'probability' => 75, 'sort_order' => 4],
            ['name' => 'Closed Won', 'probability' => 100, 'sort_order' => 5],
            ['name' => 'Closed Lost', 'probability' => 0, 'sort_order' => 6],
        ];

        foreach ($stages as $stage) {
            PipelineStage::updateOrCreate(
                ['pipeline_id' => $pipeline->id, 'name' => $stage['name']],
                $stage
            );
        }
    }
}
