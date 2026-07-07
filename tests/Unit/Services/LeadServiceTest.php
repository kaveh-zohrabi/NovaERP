<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Company;
use App\Models\Lead;
use App\Models\User;
use App\Services\LeadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadServiceTest extends TestCase
{
    use RefreshDatabase;

    private LeadService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(LeadService::class);
    }

    public function test_create_returns_lead(): void
    {
        $company = Company::factory()->create();

        $lead = $this->service->create([
            'company_id' => $company->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
        ]);

        $this->assertInstanceOf(Lead::class, $lead);
        $this->assertEquals('John', $lead->first_name);
    }

    public function test_convert_to_customer(): void
    {
        $company = Company::factory()->create();
        $lead = Lead::factory()->create(['company_id' => $company->id]);

        $customer = $this->service->convertToCustomer($lead);

        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
        $this->assertDatabaseHas('leads', ['id' => $lead->id, 'converted_customer_id' => $customer->id]);
    }

    public function test_double_conversion_throws(): void
    {
        $company = Company::factory()->create();
        $lead = Lead::factory()->create(['company_id' => $company->id]);

        $this->service->convertToCustomer($lead);

        $this->expectException(\InvalidArgumentException::class);

        $this->service->convertToCustomer($lead);
    }

    public function test_mark_lost(): void
    {
        $company = Company::factory()->create();
        $lead = Lead::factory()->create(['company_id' => $company->id, 'status' => 'new']);

        $result = $this->service->markLost($lead, 'Not interested');

        $this->assertEquals('lost', $result->status);
        $this->assertEquals('Not interested', $result->lost_reason);
    }
}
