<?php

declare(strict_types=1);

namespace Tests\Feature\Reporting;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_index_displays_available_reports(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.index'));

        $response->assertOk();
        $response->assertSee('Sales Overview');
        $response->assertSee('Inventory Valuation');
        $response->assertSee('Profit');
    }

    public function test_sales_overview_report_loads(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.show', 'sales_overview'));

        $response->assertOk();
    }

    public function test_profit_loss_report_loads(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.show', 'profit_loss'));

        $response->assertOk();
        $response->assertSee('Profit');
    }

    public function test_trial_balance_report_loads(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.show', 'trial_balance'));

        $response->assertOk();
    }

    public function test_invalid_report_type_fails(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.show', 'nonexistent'));

        $response->assertStatus(500);
    }

    public function test_csv_export_downloads(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.export', ['type' => 'sales_overview', 'format' => 'csv']));

        $response->assertStatus(200);
    }
}
