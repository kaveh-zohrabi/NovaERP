<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\NotificationPreferenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationPreferenceServiceTest extends TestCase
{
    use RefreshDatabase;

    private NotificationPreferenceService $service;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(NotificationPreferenceService::class);
        $this->user = User::factory()->create();
    }

    public function test_set_creates_preference(): void
    {
        $pref = $this->service->set($this->user->id, 'email', 'low_stock', true);

        $this->assertTrue($pref->enabled);
        $this->assertEquals('email', $pref->channel);
    }

    public function test_is_channel_enabled_returns_true_by_default(): void
    {
        $enabled = $this->service->isChannelEnabled($this->user->id, 'email', 'low_stock');

        $this->assertTrue($enabled);
    }

    public function test_is_channel_enabled_returns_false_when_disabled(): void
    {
        $this->service->set($this->user->id, 'email', 'low_stock', false);

        $enabled = $this->service->isChannelEnabled($this->user->id, 'email', 'low_stock');

        $this->assertFalse($enabled);
    }

    public function test_bulk_update(): void
    {
        $preferences = [
            ['channel' => 'email', 'notification_type' => 'low_stock', 'enabled' => false],
            ['channel' => 'in_app', 'notification_type' => 'low_stock', 'enabled' => true],
        ];

        $count = $this->service->bulkUpdate($this->user->id, $preferences);

        $this->assertEquals(2, $count);
    }

    public function test_get_user_preferences(): void
    {
        $this->service->set($this->user->id, 'email', 'low_stock', true);
        $this->service->set($this->user->id, 'in_app', 'new_lead', false);

        $prefs = $this->service->getUserPreferences($this->user->id);

        $this->assertCount(2, $prefs);
    }
}
