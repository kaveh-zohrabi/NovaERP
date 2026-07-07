<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReportService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(ReportService::class);
    }

    public function test_get_available_reports(): void
    {
        $reports = $this->service->getAvailableReports(1);

        $this->assertIsArray($reports);
        $this->assertNotEmpty($reports);
        $this->assertArrayHasKey('type', $reports[0]);
        $this->assertArrayHasKey('name', $reports[0]);
    }

    public function test_execute_sales_overview(): void
    {
        $result = $this->service->executeReport('sales_overview', ['company_id' => 1]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('total_orders', $result);
        $this->assertArrayHasKey('total_revenue', $result);
    }

    public function test_execute_inventory_valuation(): void
    {
        $result = $this->service->executeReport('inventory_valuation', ['company_id' => 1]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('total_value', $result);
    }

    public function test_execute_profit_loss(): void
    {
        $result = $this->service->executeReport('profit_loss', ['company_id' => 1]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('revenue', $result);
        $this->assertArrayHasKey('expenses', $result);
        $this->assertArrayHasKey('net_income', $result);
    }

    public function test_execute_trial_balance(): void
    {
        $result = $this->service->executeReport('trial_balance', ['company_id' => 1]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('total_debit', $result);
        $this->assertArrayHasKey('total_credit', $result);
    }

    public function test_invalid_report_type_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->service->executeReport('invalid_type', []);
    }
}
