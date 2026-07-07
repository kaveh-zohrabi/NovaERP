<?php

declare(strict_types=1);

namespace Tests\Feature\CRM;

use App\Models\Company;
use App\Models\Opportunity;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OpportunityTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Company $company;
    private Pipeline $pipeline;
    private PipelineStage $stage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->company = Company::factory()->create();
        $this->pipeline = Pipeline::factory()->create(['company_id' => $this->company->id]);
        $this->stage = PipelineStage::factory()->create(['pipeline_id' => $this->pipeline->id]);
    }

    public function test_index_displays_opportunities(): void
    {
        Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'pipeline_id' => $this->pipeline->id,
            'pipeline_stage_id' => $this->stage->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('opportunities.index'));

        $response->assertOk();
        $response->assertSee('Opportunities');
    }

    public function test_opportunity_can_be_created(): void
    {
        $response = $this->actingAs($this->user)->post(route('opportunities.store'), [
            'company_id' => $this->company->id,
            'pipeline_id' => $this->pipeline->id,
            'pipeline_stage_id' => $this->stage->id,
            'title' => 'Big Deal',
            'expected_value' => 50000,
            'probability' => 50,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('opportunities', ['title' => 'Big Deal', 'status' => 'open']);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('opportunities.store'), []);

        $response->assertSessionHasErrors(['company_id', 'pipeline_id', 'pipeline_stage_id', 'title', 'expected_value', 'probability']);
    }

    public function test_opportunity_can_be_marked_won(): void
    {
        $opp = Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'pipeline_id' => $this->pipeline->id,
            'pipeline_stage_id' => $this->stage->id,
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->user)->patch(route('opportunities.won', $opp));

        $response->assertRedirect();
        $this->assertDatabaseHas('opportunities', ['id' => $opp->id, 'status' => 'won']);
    }

    public function test_opportunity_can_be_marked_lost_with_reason(): void
    {
        $opp = Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'pipeline_id' => $this->pipeline->id,
            'pipeline_stage_id' => $this->stage->id,
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->user)->patch(route('opportunities.lost', $opp), [
            'reason' => 'Too expensive',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('opportunities', ['id' => $opp->id, 'status' => 'lost', 'lost_reason' => 'Too expensive']);
    }

    public function test_search_filters_opportunities(): void
    {
        Opportunity::factory()->create([
            'company_id' => $this->company->id,
            'pipeline_id' => $this->pipeline->id,
            'pipeline_stage_id' => $this->stage->id,
            'title' => 'Unique Deal Name',
        ]);

        $response = $this->actingAs($this->user)->get(route('opportunities.index', ['search' => 'Unique Deal']));

        $response->assertOk();
        $response->assertSee('Unique Deal Name');
    }
}
