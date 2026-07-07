<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\AnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsServiceTest extends TestCase
{
    use RefreshDatabase;

    private AnalyticsService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(AnalyticsService::class);
    }

    public function test_get_executive_metrics_returns_array(): void
    {
        $metrics = $this->service->getExecutiveMetrics(1);

        $this->assertIsArray($metrics);
        $this->assertArrayHasKey('revenue', $metrics);
        $this->assertArrayHasKey('expenses', $metrics);
        $this->assertArrayHasKey('net_profit', $metrics);
        $this->assertArrayHasKey('total_customers', $metrics);
        $this->assertArrayHasKey('total_leads', $metrics);
        $this->assertArrayHasKey('lead_conversion_rate', $metrics);
    }

    public function test_executive_metrics_with_date_range(): void
    {
        $metrics = $this->service->getExecutiveMetrics(1, '2026-01-01', '2026-12-31');

        $this->assertIsArray($metrics);
        $this->assertEquals(0.0, $metrics['lead_conversion_rate']);
    }
}
