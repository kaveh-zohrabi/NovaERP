<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        return [
            'company_id' => 1,
            'uploaded_by' => User::factory(),
            'file_name' => fake()->uuid().'.pdf',
            'original_name' => fake()->unique()->word().'.pdf',
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'file_size' => fake()->numberBetween(1024, 10485760),
            'disk' => 'local',
            'path' => 'documents/general/'.fake()->uuid().'.pdf',
            'checksum' => hash('sha256', fake()->uuid()),
            'description' => fake()->optional()->sentence(),
            'is_public' => false,
        ];
    }
}
