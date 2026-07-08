<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditServiceTest extends TestCase
{
    use RefreshDatabase;

    private AuditService $service;
    private Company $company;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(AuditService::class);
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create();
    }

    public function test_log_creates_audit_record(): void
    {
        $log = $this->service->log([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'event' => 'created',
            'auditable_type' => User::class,
            'auditable_id' => $this->user->id,
        ]);

        $this->assertInstanceOf(AuditLog::class, $log);
        $this->assertEquals('created', $log->event);
    }

    public function test_log_event_with_model(): void
    {
        $log = $this->service->logEvent('updated', $this->user, ['name' => 'Old'], ['name' => 'New']);

        $this->assertEquals('updated', $log->event);
        $this->assertEquals(User::class, $log->auditable_type);
        $this->assertEquals(['name' => 'Old'], $log->old_values);
        $this->assertEquals(['name' => 'New'], $log->new_values);
    }

    public function test_get_history_returns_collection(): void
    {
        AuditLog::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'auditable_type' => User::class,
            'auditable_id' => $this->user->id,
        ]);

        $history = $this->service->getHistory($this->user);

        $this->assertGreaterThanOrEqual(3, $history->count());
    }

    public function test_query_returns_paginated(): void
    {
        AuditLog::factory()->count(5)->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);

        $result = $this->service->query($this->company->id);

        $this->assertGreaterThanOrEqual(5, $result->total());
    }

    public function test_query_filters_by_event(): void
    {
        AuditLog::factory()->create(['company_id' => $this->company->id, 'user_id' => $this->user->id, 'event' => 'approved']);
        AuditLog::factory()->create(['company_id' => $this->company->id, 'user_id' => $this->user->id, 'event' => 'rejected']);

        $result = $this->service->query($this->company->id, ['event' => 'approved']);

        $this->assertGreaterThanOrEqual(1, $result->total());
    }

    public function test_log_sets_ip_address(): void
    {
        $log = $this->service->log([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'event' => 'login',
        ]);

        $this->assertNotEmpty($log->ip_address);
    }
}
