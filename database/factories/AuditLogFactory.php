<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        return [
            'company_id' => 1,
            'user_id' => User::factory(),
            'event' => fake()->randomElement(['created', 'updated', 'deleted', 'restored', 'approved', 'posted']),
            'auditable_type' => \App\Models\User::class,
            'auditable_id' => 1,
            'old_values' => null,
            'new_values' => null,
            'ip_address' => fake()->ipv4(),
            'user_agent' => 'Mozilla/5.0',
            'request_id' => fake()->uuid(),
            'metadata' => null,
            'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
