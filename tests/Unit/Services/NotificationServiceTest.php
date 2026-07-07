<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Company;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    private NotificationService $service;
    private Company $company;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(NotificationService::class);
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create();
    }

    public function test_create_returns_notification(): void
    {
        $notif = $this->service->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'type' => 'test',
            'title' => 'Test Notification',
            'message' => 'This is a test.',
            'priority' => 'normal',
            'status' => 'unread',
        ]);

        $this->assertInstanceOf(UserNotification::class, $notif);
        $this->assertEquals('Test Notification', $notif->title);
    }

    public function test_duplicate_notification_returns_existing(): void
    {
        $data = [
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'type' => 'duplicate_test',
            'title' => 'Duplicate',
            'message' => 'Same message',
            'priority' => 'normal',
            'status' => 'unread',
        ];

        $first = $this->service->create($data);
        $second = $this->service->create($data);

        $this->assertEquals($first->id, $second->id);
    }

    public function test_mark_as_read(): void
    {
        $notif = UserNotification::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'status' => 'unread',
        ]);

        $result = $this->service->markAsRead($notif);

        $this->assertEquals('read', $result->status);
        $this->assertNotNull($result->read_at);
    }

    public function test_mark_all_as_read(): void
    {
        UserNotification::factory()->count(5)->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'status' => 'unread',
        ]);

        $count = $this->service->markAllAsRead($this->user->id, $this->company->id);

        $this->assertEquals(5, $count);
    }

    public function test_get_unread_count(): void
    {
        UserNotification::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'status' => 'unread',
        ]);

        $this->assertEquals(3, $this->service->getUnreadCount($this->user->id));
    }

    public function test_search_returns_paginated(): void
    {
        UserNotification::factory()->count(5)->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);

        $result = $this->service->getUserNotifications($this->user->id, $this->company->id);

        $this->assertEquals(5, $result->total());
    }

    public function test_delete_removes_notification(): void
    {
        $notif = UserNotification::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);

        $result = $this->service->delete($notif);

        $this->assertTrue($result['success']);
        $this->assertDatabaseMissing('user_notifications', ['id' => $notif->id]);
    }
}
