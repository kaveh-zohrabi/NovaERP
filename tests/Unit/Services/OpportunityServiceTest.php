<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Company;
use App\Models\Opportunity;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Services\OpportunityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OpportunityServiceTest extends TestCase
{
    use RefreshDatabase;

    private OpportunityService $service;
    private Company $company;
    private Pipeline $pipeline;
    private PipelineStage $stage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(OpportunityService::class);
        $this->company = Company::factory()->create();
        $this->pipeline = Pipeline::factory()->create(['company_id' => $this->company->id]);
        $this->stage = PipelineStage::factory()->create(['pipeline_id' => $this->pipeline->id]);
    }

    public function test_create_returns_opportunity(): void
    {
        $opp = $this->service->create([
            'company_id' => $this->company->id,
            'pipeline_id' => $this->pipeline->id,
            'pipeline_stage_id' => $this->stage->id,
            'title' => 'Test Deal',
            'expected_value' => 10000,
            'probability' => 50,
        ]);

        $this->assertInstanceOf(Opportunity::class, $opp);
        $this->assertEquals('open', $opp->fresh()->status);
    }

    public function test_mark_won(): void
    {
        $opp = Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'pipeline_id' => $this->pipeline->id,
            'pipeline_stage_id' => $this->stage->id,
            'status' => 'open',
        ]);

        $result = $this->service->markWon($opp);

        $this->assertEquals('won', $result->status);
        $this->assertEquals(100, $result->probability);
    }

    public function test_mark_lost(): void
    {
        $opp = Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'pipeline_id' => $this->pipeline->id,
            'pipeline_stage_id' => $this->stage->id,
            'status' => 'open',
        ]);

        $result = $this->service->markLost($opp, 'Too expensive');

        $this->assertEquals('lost', $result->status);
        $this->assertEquals('Too expensive', $result->lost_reason);
    }

    public function test_cannot_mark_won_when_not_open(): void
    {
        $opp = Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'pipeline_id' => $this->pipeline->id,
            'pipeline_stage_id' => $this->stage->id,
            'status' => 'won',
        ]);

        $this->expectException(\InvalidArgumentException::class);

        $this->service->markWon($opp);
    }

    public function test_cannot_mark_lost_when_not_open(): void
    {
        $opp = Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'pipeline_id' => $this->pipeline->id,
            'pipeline_stage_id' => $this->stage->id,
            'status' => 'lost',
        ]);

        $this->expectException(\InvalidArgumentException::class);

        $this->service->markLost($opp, 'Already lost');
    }

    public function test_move_to_stage(): void
    {
        $newStage = PipelineStage::factory()->create(['pipeline_id' => $this->pipeline->id]);
        $opp = Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'pipeline_id' => $this->pipeline->id,
            'pipeline_stage_id' => $this->stage->id,
            'status' => 'open',
        ]);

        $result = $this->service->moveToStage($opp, $newStage->id);

        $this->assertEquals($newStage->id, $result->pipeline_stage_id);
    }

    public function test_cannot_move_stage_when_not_open(): void
    {
        $opp = Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'pipeline_id' => $this->pipeline->id,
            'pipeline_stage_id' => $this->stage->id,
            'status' => 'won',
        ]);

        $this->expectException(\InvalidArgumentException::class);

        $this->service->moveToStage($opp, $this->stage->id);
    }
}
