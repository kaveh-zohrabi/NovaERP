<?php

declare(strict_types=1);

namespace Tests\Feature\Audit;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditTest extends TestCase
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

    public function test_index_displays_audit_logs(): void
    {
        AuditLog::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('audit.index'));

        $response->assertOk();
        $response->assertSee('Audit Log');
    }

    public function test_show_displays_audit_detail(): void
    {
        $log = AuditLog::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('audit.show', $log));

        $response->assertOk();
        $response->assertSee('Audit Detail');
    }

    public function test_activity_timeline_loads(): void
    {
        $response = $this->actingAs($this->user)->get(route('audit.activity'));

        $response->assertOk();
        $response->assertSee('Activity Timeline');
    }

    public function test_search_filters_audit_logs(): void
    {
        AuditLog::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'event' => 'created',
        ]);

        $response = $this->actingAs($this->user)->get(route('audit.index', ['event' => 'created']));

        $response->assertOk();
    }

    public function test_export_audit_logs(): void
    {
        $response = $this->actingAs($this->user)->get(route('audit.export', ['format' => 'csv']));

        $response->assertStatus(200);
    }
}
