<?php

declare(strict_types=1);

namespace Tests\Feature\Reporting;

use App\Models\Company;
use App\Models\Dashboard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
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

    public function test_index_displays_dashboards(): void
    {
        Dashboard::factory()->create([
            'company_id' => $this->company->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboards.index'));

        $response->assertOk();
    }

    public function test_dashboard_can_be_created(): void
    {
        $response = $this->actingAs($this->user)->post(route('dashboards.store'), [
            'name' => 'My Dashboard',
            'description' => 'Executive overview',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('dashboards', ['name' => 'My Dashboard']);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('dashboards.store'), []);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_executive_dashboard_loads(): void
    {
        $response = $this->actingAs($this->user)->get(route('executive'));

        $response->assertOk();
        $response->assertSee('Executive Dashboard');
    }
}
