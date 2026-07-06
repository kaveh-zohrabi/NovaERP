<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function test_index_displays_companies(): void
    {
        Company::factory()->count(3)->create();

        $response = $this->actingAs($this->user)->get(route('companies.index'));

        $response->assertOk();
        $response->assertSee('Companies');
    }

    public function test_index_can_search_companies(): void
    {
        Company::factory()->create(['name' => 'Acme Corp']);
        Company::factory()->create(['name' => 'Beta Inc']);

        $response = $this->actingAs($this->user)->get(route('companies.index', ['search' => 'Acme']));

        $response->assertOk();
        $response->assertSee('Acme Corp');
        $response->assertDontSee('Beta Inc');
    }

    public function test_unauthenticated_user_cannot_access_index(): void
    {
        $response = $this->get(route('companies.index'));

        $response->assertRedirect('/login');
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function test_create_form_can_be_rendered(): void
    {
        $response = $this->actingAs($this->user)->get(route('companies.create'));

        $response->assertOk();
        $response->assertSee('Create Company');
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function test_company_can_be_created(): void
    {
        $response = $this->actingAs($this->user)->post(route('companies.store'), [
            'name' => 'Acme Corp',
            'slug' => 'acme-corp',
            'email' => 'info@acme.com',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('companies', [
            'name' => 'Acme Corp',
            'slug' => 'acme-corp',
        ]);
    }

    public function test_creator_is_assigned_to_company(): void
    {
        $this->actingAs($this->user)->post(route('companies.store'), [
            'name' => 'Acme Corp',
            'slug' => 'acme-corp',
            'email' => 'info@acme.com',
            'status' => 'active',
        ]);

        $company = Company::where('slug', 'acme-corp')->first();

        $this->assertTrue($company->users->contains($this->user));
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('companies.store'), []);

        $response->assertSessionHasErrors(['name', 'slug', 'email', 'status']);
    }

    public function test_store_validates_unique_slug(): void
    {
        Company::factory()->create(['slug' => 'acme-corp']);

        $response = $this->actingAs($this->user)->post(route('companies.store'), [
            'name' => 'Acme Corp',
            'slug' => 'acme-corp',
            'email' => 'info@acme.com',
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('slug');
    }

    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    public function test_show_displays_company(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($this->user)->get(route('companies.show', $company));

        $response->assertOk();
        $response->assertSee($company->name);
    }

    /*
    |--------------------------------------------------------------------------
    | Edit & Update
    |--------------------------------------------------------------------------
    */

    public function test_edit_form_can_be_rendered(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($this->user)->get(route('companies.edit', $company));

        $response->assertOk();
        $response->assertSee('Edit Company');
        $response->assertSee($company->name);
    }

    public function test_company_can_be_updated(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($this->user)->put(route('companies.update', $company), [
            'name' => 'Updated Corp',
            'slug' => $company->slug,
            'email' => 'updated@example.com',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'Updated Corp',
        ]);
    }

    public function test_update_validates_required_fields(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($this->user)->put(route('companies.update', $company), []);

        $response->assertSessionHasErrors(['name', 'slug', 'email', 'status']);
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    public function test_company_can_be_soft_deleted(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('companies.destroy', $company));

        $response->assertRedirect();
        $this->assertSoftDeleted('companies', ['id' => $company->id]);
    }

    public function test_company_with_users_cannot_be_deleted(): void
    {
        $company = Company::factory()->create();
        $company->users()->attach($this->user);

        $response = $this->actingAs($this->user)->delete(route('companies.destroy', $company));

        $response->assertRedirect();
        $this->assertDatabaseHas('companies', ['id' => $company->id]);
        $this->assertDatabaseHas('company_user', ['company_id' => $company->id]);
    }

    /*
    |--------------------------------------------------------------------------
    | Restore
    |--------------------------------------------------------------------------
    */

    public function test_soft_deleted_company_can_be_restored(): void
    {
        $company = Company::factory()->create();
        $company->delete();

        $response = $this->actingAs($this->user)->post(route('companies.restore', $company));

        $response->assertRedirect();
        $this->assertDatabaseHas('companies', ['id' => $company->id, 'deleted_at' => null]);
    }

    /*
    |--------------------------------------------------------------------------
    | Force Delete
    |--------------------------------------------------------------------------
    */

    public function test_company_can_be_force_deleted(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('companies.force-delete', $company));

        $response->assertRedirect(route('companies.index'));
        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    }

    /*
    |--------------------------------------------------------------------------
    | Activate / Deactivate
    |--------------------------------------------------------------------------
    */

    public function test_company_can_be_activated(): void
    {
        $company = Company::factory()->inactive()->create();

        $response = $this->actingAs($this->user)->patch(route('companies.activate', $company));

        $response->assertRedirect();
        $this->assertDatabaseHas('companies', ['id' => $company->id, 'status' => 'active']);
    }

    public function test_company_can_be_deactivated(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($this->user)->patch(route('companies.deactivate', $company));

        $response->assertRedirect();
        $this->assertDatabaseHas('companies', ['id' => $company->id, 'status' => 'inactive']);
    }
}
