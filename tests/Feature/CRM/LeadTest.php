<?php

declare(strict_types=1);

namespace Tests\Feature\CRM;

use App\Models\Company;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadTest extends TestCase
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

    public function test_index_displays_leads(): void
    {
        Lead::factory()->count(3)->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->user)->get(route('leads.index'));

        $response->assertOk();
        $response->assertSee('Leads');
    }

    public function test_lead_can_be_created(): void
    {
        $response = $this->actingAs($this->user)->post(route('leads.store'), [
            'company_id' => $this->company->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '555-0100',
            'company_name' => 'Acme Corp',
            'source' => 'website',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('leads', ['first_name' => 'John', 'last_name' => 'Doe']);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('leads.store'), []);

        $response->assertSessionHasErrors(['company_id', 'first_name', 'last_name']);
    }

    public function test_store_validates_unique_email_per_company(): void
    {
        Lead::factory()->create(['company_id' => $this->company->id, 'email' => 'taken@example.com']);

        $response = $this->actingAs($this->user)->post(route('leads.store'), [
            'company_id' => $this->company->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'taken@example.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_lead_can_be_updated(): void
    {
        $lead = Lead::factory()->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->user)->put(route('leads.update', $lead), [
            'first_name' => 'Updated',
            'last_name' => 'Lead',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('leads', ['id' => $lead->id, 'first_name' => 'Updated']);
    }

    public function test_lead_can_be_deleted(): void
    {
        $lead = Lead::factory()->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->user)->delete(route('leads.destroy', $lead));

        $response->assertRedirect();
        $this->assertSoftDeleted('leads', ['id' => $lead->id]);
    }

    public function test_search_filters_leads(): void
    {
        Lead::factory()->create(['company_id' => $this->company->id, 'first_name' => 'Alice']);
        Lead::factory()->create(['company_id' => $this->company->id, 'first_name' => 'Bob']);

        $response = $this->actingAs($this->user)->get(route('leads.index', ['search' => 'Alice']));

        $response->assertOk();
        $response->assertSee('Alice');
    }
}
