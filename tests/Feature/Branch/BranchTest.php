<?php

declare(strict_types=1);

namespace Tests\Feature\Branch;

use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchTest extends TestCase
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

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function test_index_displays_branches(): void
    {
        Branch::factory()->count(3)->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->user)->get(route('branches.index'));

        $response->assertOk();
        $response->assertSee('Branches');
    }

    public function test_index_can_search_branches(): void
    {
        Branch::factory()->create(['company_id' => $this->company->id, 'name' => 'New York Office']);
        Branch::factory()->create(['company_id' => $this->company->id, 'name' => 'London Office']);

        $response = $this->actingAs($this->user)->get(route('branches.index', ['search' => 'New York']));

        $response->assertOk();
        $response->assertSee('New York Office');
        $response->assertDontSee('London Office');
    }

    public function test_unauthenticated_user_cannot_access_index(): void
    {
        $response = $this->get(route('branches.index'));

        $response->assertRedirect('/login');
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function test_branch_can_be_created(): void
    {
        $response = $this->actingAs($this->user)->post(route('branches.store'), [
            'company_id' => $this->company->id,
            'name' => 'New York Office',
            'slug' => 'new-york-office',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('branches', ['name' => 'New York Office']);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('branches.store'), []);

        $response->assertSessionHasErrors(['company_id', 'name', 'slug', 'status']);
    }

    public function test_store_validates_unique_slug_per_company(): void
    {
        Branch::factory()->create(['company_id' => $this->company->id, 'slug' => 'ny-office']);

        $response = $this->actingAs($this->user)->post(route('branches.store'), [
            'company_id' => $this->company->id,
            'name' => 'New York Office',
            'slug' => 'ny-office',
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('slug');
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function test_branch_can_be_updated(): void
    {
        $branch = Branch::factory()->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->user)->put(route('branches.update', $branch), [
            'name' => 'Updated Branch',
            'slug' => $branch->slug,
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('branches', ['id' => $branch->id, 'name' => 'Updated Branch']);
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    public function test_branch_can_be_soft_deleted(): void
    {
        $branch = Branch::factory()->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->user)->delete(route('branches.destroy', $branch));

        $response->assertRedirect();
        $this->assertSoftDeleted('branches', ['id' => $branch->id]);
    }

    /*
    |--------------------------------------------------------------------------
    | Restore
    |--------------------------------------------------------------------------
    */

    public function test_soft_deleted_branch_can_be_restored(): void
    {
        $branch = Branch::factory()->create(['company_id' => $this->company->id]);
        $branch->delete();

        $response = $this->actingAs($this->user)->post(route('branches.restore', $branch));

        $response->assertRedirect();
        $this->assertDatabaseHas('branches', ['id' => $branch->id, 'deleted_at' => null]);
    }

    /*
    |--------------------------------------------------------------------------
    | Activate / Deactivate
    |--------------------------------------------------------------------------
    */

    public function test_branch_can_be_activated(): void
    {
        $branch = Branch::factory()->inactive()->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->user)->patch(route('branches.activate', $branch));

        $response->assertRedirect();
        $this->assertDatabaseHas('branches', ['id' => $branch->id, 'status' => 'active']);
    }

    public function test_branch_can_be_deactivated(): void
    {
        $branch = Branch::factory()->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->user)->patch(route('branches.deactivate', $branch));

        $response->assertRedirect();
        $this->assertDatabaseHas('branches', ['id' => $branch->id, 'status' => 'inactive']);
    }
}
