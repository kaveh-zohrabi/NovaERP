<?php

declare(strict_types=1);

namespace Tests\Feature\Position;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PositionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Company $company;

    private Branch $branch;

    private Department $department;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->company = Company::factory()->create();
        $this->branch = Branch::factory()->create(['company_id' => $this->company->id]);
        $this->department = Department::factory()->create([
            'branch_id' => $this->branch->id,
            'company_id' => $this->company->id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function test_index_displays_positions(): void
    {
        Position::factory()->count(3)->create([
            'department_id' => $this->department->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('positions.index'));

        $response->assertOk();
        $response->assertSee('Positions');
    }

    public function test_index_can_search_positions(): void
    {
        Position::factory()->create(['department_id' => $this->department->id, 'company_id' => $this->company->id, 'name' => 'Sales Manager']);
        Position::factory()->create(['department_id' => $this->department->id, 'company_id' => $this->company->id, 'name' => 'HR Specialist']);

        $response = $this->actingAs($this->user)->get(route('positions.index', ['search' => 'Sales']));

        $response->assertOk();
        $response->assertSee('Sales Manager');
        $response->assertDontSee('HR Specialist');
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function test_position_can_be_created(): void
    {
        $response = $this->actingAs($this->user)->post(route('positions.store'), [
            'department_id' => $this->department->id,
            'company_id' => $this->company->id,
            'name' => 'Sales Manager',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('positions', ['name' => 'Sales Manager']);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('positions.store'), []);

        $response->assertSessionHasErrors(['department_id', 'company_id', 'name', 'status']);
    }

    public function test_store_validates_salary_range(): void
    {
        $response = $this->actingAs($this->user)->post(route('positions.store'), [
            'department_id' => $this->department->id,
            'company_id' => $this->company->id,
            'name' => 'Sales Manager',
            'min_salary' => 80000,
            'max_salary' => 50000,
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('max_salary');
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function test_position_can_be_updated(): void
    {
        $position = Position::factory()->create([
            'department_id' => $this->department->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)->put(route('positions.update', $position), [
            'name' => 'Updated Position',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('positions', ['id' => $position->id, 'name' => 'Updated Position']);
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    public function test_position_can_be_soft_deleted(): void
    {
        $position = Position::factory()->create([
            'department_id' => $this->department->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('positions.destroy', $position));

        $response->assertRedirect();
        $this->assertSoftDeleted('positions', ['id' => $position->id]);
    }

    /*
    |--------------------------------------------------------------------------
    | Restore
    |--------------------------------------------------------------------------
    */

    public function test_soft_deleted_position_can_be_restored(): void
    {
        $position = Position::factory()->create([
            'department_id' => $this->department->id,
            'company_id' => $this->company->id,
        ]);
        $position->delete();

        $response = $this->actingAs($this->user)->post(route('positions.restore', $position));

        $response->assertRedirect();
        $this->assertDatabaseHas('positions', ['id' => $position->id, 'deleted_at' => null]);
    }

    /*
    |--------------------------------------------------------------------------
    | Activate / Deactivate
    |--------------------------------------------------------------------------
    */

    public function test_position_can_be_activated(): void
    {
        $position = Position::factory()->inactive()->create([
            'department_id' => $this->department->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)->patch(route('positions.activate', $position));

        $response->assertRedirect();
        $this->assertDatabaseHas('positions', ['id' => $position->id, 'status' => 'active']);
    }

    public function test_position_can_be_deactivated(): void
    {
        $position = Position::factory()->create([
            'department_id' => $this->department->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)->patch(route('positions.deactivate', $position));

        $response->assertRedirect();
        $this->assertDatabaseHas('positions', ['id' => $position->id, 'status' => 'inactive']);
    }
}
