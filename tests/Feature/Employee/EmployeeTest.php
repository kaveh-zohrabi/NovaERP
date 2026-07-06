<?php

declare(strict_types=1);

namespace Tests\Feature\Employee;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Company $company;

    private Branch $branch;

    private Department $department;

    private Position $position;

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
        $this->position = Position::factory()->create([
            'department_id' => $this->department->id,
            'company_id' => $this->company->id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function test_index_displays_employees(): void
    {
        Employee::factory()->count(3)->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->user)->get(route('employees.index'));

        $response->assertOk();
        $response->assertSee('Employees');
    }

    public function test_index_can_search_employees(): void
    {
        Employee::factory()->create(['company_id' => $this->company->id, 'first_name' => 'John', 'last_name' => 'Doe']);
        Employee::factory()->create(['company_id' => $this->company->id, 'first_name' => 'Jane', 'last_name' => 'Smith']);

        $response = $this->actingAs($this->user)->get(route('employees.index', ['search' => 'John']));

        $response->assertOk();
        $response->assertSee('John Doe');
        $response->assertDontSee('Jane Smith');
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function test_employee_can_be_created(): void
    {
        $response = $this->actingAs($this->user)->post(route('employees.store'), [
            'company_id' => $this->company->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'hire_date' => '2024-01-01',
            'status' => 'active',
            'employment_type' => 'full_time',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('employees', ['email' => 'john@example.com']);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('employees.store'), []);

        $response->assertSessionHasErrors(['company_id', 'first_name', 'last_name', 'email', 'hire_date', 'status', 'employment_type']);
    }

    public function test_store_validates_unique_email(): void
    {
        Employee::factory()->create(['email' => 'john@example.com']);

        $response = $this->actingAs($this->user)->post(route('employees.store'), [
            'company_id' => $this->company->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'hire_date' => '2024-01-01',
            'status' => 'active',
            'employment_type' => 'full_time',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_store_validates_salary_range(): void
    {
        $response = $this->actingAs($this->user)->post(route('employees.store'), [
            'company_id' => $this->company->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'hire_date' => '2024-01-01',
            'status' => 'active',
            'employment_type' => 'full_time',
            'salary' => -1000,
        ]);

        $response->assertSessionHasErrors('salary');
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function test_employee_can_be_updated(): void
    {
        $employee = Employee::factory()->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->user)->put(route('employees.update', $employee), [
            'company_id' => $this->company->id,
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'email' => $employee->email,
            'hire_date' => $employee->hire_date->format('Y-m-d'),
            'status' => 'active',
            'employment_type' => 'full_time',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('employees', ['id' => $employee->id, 'first_name' => 'Updated']);
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    public function test_employee_can_be_soft_deleted(): void
    {
        $employee = Employee::factory()->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->user)->delete(route('employees.destroy', $employee));

        $response->assertRedirect();
        $this->assertSoftDeleted('employees', ['id' => $employee->id]);
    }

    /*
    |--------------------------------------------------------------------------
    | Restore
    |--------------------------------------------------------------------------
    */

    public function test_soft_deleted_employee_can_be_restored(): void
    {
        $employee = Employee::factory()->create(['company_id' => $this->company->id]);
        $employee->delete();

        $response = $this->actingAs($this->user)->post(route('employees.restore', $employee));

        $response->assertRedirect();
        $this->assertDatabaseHas('employees', ['id' => $employee->id, 'deleted_at' => null]);
    }

    /*
    |--------------------------------------------------------------------------
    | Lifecycle
    |--------------------------------------------------------------------------
    */

    public function test_employee_can_be_terminated(): void
    {
        $employee = Employee::factory()->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->user)->patch(route('employees.terminate', $employee));

        $response->assertRedirect();
        $this->assertDatabaseHas('employees', ['id' => $employee->id, 'status' => 'terminated']);
    }

    public function test_employee_can_be_reactivated(): void
    {
        $employee = Employee::factory()->terminated()->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->user)->patch(route('employees.reactivate', $employee));

        $response->assertRedirect();
        $this->assertDatabaseHas('employees', ['id' => $employee->id, 'status' => 'active']);
    }
}
