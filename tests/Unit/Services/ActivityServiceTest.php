<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\User;
use App\Services\ActivityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityServiceTest extends TestCase
{
    use RefreshDatabase;

    private ActivityService $service;
    private Company $company;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(ActivityService::class);
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create();
    }

    public function test_log_creates_activity(): void
    {
        $activity = $this->service->log([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'activity_type' => 'login',
            'description' => 'User logged in',
        ]);

        $this->assertInstanceOf(ActivityLog::class, $activity);
        $this->assertEquals('login', $activity->activity_type);
    }

    public function test_record_creates_activity(): void
    {
        $activity = $this->service->record('created', 'User created product', $this->user);

        $this->assertEquals('created', $activity->activity_type);
        $this->assertEquals(User::class, $activity->subject_type);
    }

    public function test_get_for_subject(): void
    {
        ActivityLog::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'subject_type' => User::class,
            'subject_id' => $this->user->id,
        ]);

        $result = $this->service->getForSubject($this->user);

        $this->assertCount(3, $result);
    }

    public function test_query_returns_paginated(): void
    {
        ActivityLog::factory()->count(5)->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);

        $result = $this->service->query($this->company->id);

        $this->assertEquals(5, $result->total());
    }
}
