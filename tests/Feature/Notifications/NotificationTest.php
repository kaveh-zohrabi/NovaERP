<?php

declare(strict_types=1);

namespace Tests\Feature\Notifications;

use App\Models\Company;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();
        $this->user = User::factory()->create();
    }

    public function test_index_displays_notifications(): void
    {
        UserNotification::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('notifications.index'));

        $response->assertOk();
        $response->assertSee('Notifications');
    }

    public function test_notification_can_be_marked_as_read(): void
    {
        $notif = UserNotification::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'status' => 'unread',
        ]);

        $response = $this->actingAs($this->user)->patch(route('notifications.mark-read', $notif));

        $response->assertRedirect();
        $this->assertDatabaseHas('user_notifications', ['id' => $notif->id, 'status' => 'read']);
    }

    public function test_mark_all_as_read(): void
    {
        UserNotification::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'status' => 'unread',
        ]);

        $response = $this->actingAs($this->user)->patch(route('notifications.mark-all-read'));

        $response->assertRedirect();
        $this->assertEquals(0, UserNotification::where('user_id', $this->user->id)->where('status', 'unread')->count());
    }

    public function test_notification_can_be_deleted(): void
    {
        $notif = UserNotification::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('notifications.destroy', $notif));

        $response->assertRedirect();
        $this->assertDatabaseMissing('user_notifications', ['id' => $notif->id]);
    }

    public function test_notification_can_be_archived(): void
    {
        $notif = UserNotification::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->patch(route('notifications.archive', $notif));

        $response->assertRedirect();
        $this->assertDatabaseHas('user_notifications', ['id' => $notif->id, 'status' => 'archived']);
    }

    public function test_unread_count_returns_json(): void
    {
        UserNotification::factory()->count(2)->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'status' => 'unread',
        ]);

        $response = $this->actingAs($this->user)->get(route('notifications.unread-count'));

        $response->assertOk();
        $response->assertJson(['count' => 2]);
    }

    public function test_search_filters_notifications(): void
    {
        UserNotification::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'title' => 'Low Stock Alert',
        ]);

        $response = $this->actingAs($this->user)->get(route('notifications.index', ['search' => 'Low Stock']));

        $response->assertOk();
        $response->assertSee('Low Stock Alert');
    }

    public function test_show_displays_notification(): void
    {
        $notif = UserNotification::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('notifications.show', $notif));

        $response->assertOk();
        $response->assertSee($notif->title);
    }
}
