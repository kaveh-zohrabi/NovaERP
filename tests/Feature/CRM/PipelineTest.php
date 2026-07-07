<?php

declare(strict_types=1);

namespace Tests\Feature\CRM;

use App\Models\Company;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PipelineTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->company = Company::factory()->create();
    }

    public function test_index_displays_pipelines(): void
    {
        Pipeline::factory()->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->user)->get(route('pipelines.index'));

        $response->assertOk();
    }

    public function test_pipeline_can_be_created(): void
    {
        $response = $this->actingAs($this->user)->post(route('pipelines.store'), [
            'company_id' => $this->company->id,
            'name' => 'New Pipeline',
            'description' => 'Test pipeline',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pipelines', ['name' => 'New Pipeline']);
    }

    public function test_stage_can_be_added_to_pipeline(): void
    {
        $pipeline = Pipeline::factory()->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->user)->post(route('pipelines.stages.store', $pipeline), [
            'name' => 'Qualification',
            'probability' => 25,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pipeline_stages', ['name' => 'Qualification', 'pipeline_id' => $pipeline->id]);
    }

    public function test_stage_can_be_removed(): void
    {
        $pipeline = Pipeline::factory()->create(['company_id' => $this->company->id]);
        $stage = PipelineStage::factory()->create(['pipeline_id' => $pipeline->id]);

        $response = $this->actingAs($this->user)->delete(route('pipelines.stages.destroy', $stage));

        $response->assertRedirect();
        $this->assertDatabaseMissing('pipeline_stages', ['id' => $stage->id]);
    }
}
