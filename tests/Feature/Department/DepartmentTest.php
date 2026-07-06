<?php

declare(strict_types=1);

namespace Tests\Feature\Department;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Company $company;

    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->company = Company::factory()->create();
        $this->branch = Branch::factory()->create(['company_id' => $this->company->id]);
    }

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function test_index_displays_departments(): void
    {
        Department::factory()->count(3)->create([
            'branch_id' => $this->branch->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('departments.index'));

        $response->assertOk();
        $response->assertSee('Departments');
    }

    public function test_index_can_search_departments(): void
    {
        Department::factory()->create(['branch_id' => $this->branch->id, 'company_id' => $this->company->id, 'name' => 'Sales']);
        Department::factory()->create(['branch_id' => $this->branch->id, 'company_id' => $this->company->id, 'name' => 'Marketing']);

        $response = $this->actingAs($this->user)->get(route('departments.index', ['search' => 'Sales']));

        $response->assertOk();
        $response->assertSee('Sales');
        $response->assertDontSee('Marketing');
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function test_department_can_be_created(): void
    {
        $response = $this->actingAs($this->user)->post(route('departments.store'), [
            'branch_id' => $this->branch->id,
            'company_id' => $this->company->id,
            'name' => 'Sales',
            'slug' => 'sales',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('departments', ['name' => 'Sales']);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('departments.store'), []);

        $response->assertSessionHasErrors(['branch_id', 'company_id', 'name', 'slug', 'status']);
    }

    public function test_store_validates_unique_slug_per_branch(): void
    {
        Department::factory()->create(['branch_id' => $this->branch->id, 'slug' => 'sales']);

        $response = $this->actingAs($this->user)->post(route('departments.store'), [
            'branch_id' => $this->branch->id,
            'company_id' => $this->company->id,
            'name' => 'Sales',
            'slug' => 'sales',
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('slug');
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function test_department_can_be_updated(): void
    {
        $department = Department::factory()->create([
            'branch_id' => $this->branch->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)->put(route('departments.update', $department), [
            'name' => 'Updated Department',
            'slug' => $department->slug,
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('departments', ['id' => $department->id, 'name' => 'Updated Department']);
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    public function test_department_can_be_soft_deleted(): void
    {
        $department = Department::factory()->create([
            'branch_id' => $this->branch->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('departments.destroy', $department));

        $response->assertRedirect();
        $this->assertSoftDeleted('departments', ['id' => $department->id]);
    }

    /*
    |--------------------------------------------------------------------------
    | Restore
    |--------------------------------------------------------------------------
    */

    public function test_soft_deleted_department_can_be_restored(): void
    {
        $department = Department::factory()->create([
            'branch_id' => $this->branch->id,
            'company_id' => $this->company->id,
        ]);
        $department->delete();

        $response = $this->actingAs($this->user)->post(route('departments.restore', $department));

        $response->assertRedirect();
        $this->assertDatabaseHas('departments', ['id' => $department->id, 'deleted_at' => null]);
    }

    /*
    |--------------------------------------------------------------------------
    | Activate / Deactivate
    |--------------------------------------------------------------------------
    */

    public function test_department_can_be_activated(): void
    {
        $department = Department::factory()->inactive()->create([
            'branch_id' => $this->branch->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)->patch(route('departments.activate', $department));

        $response->assertRedirect();
        $this->assertDatabaseHas('departments', ['id' => $department->id, 'status' => 'active']);
    }

    public function test_department_can_be_deactivated(): void
    {
        $department = Department::factory()->create([
            'branch_id' => $this->branch->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)->patch(route('departments.deactivate', $department));

        $response->assertRedirect();
        $this->assertDatabaseHas('departments', ['id' => $department->id, 'status' => 'inactive']);
    }
}
