<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use App\Services\DepartmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentServiceTest extends TestCase
{
    use RefreshDatabase;

    private DepartmentService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(DepartmentService::class);
    }

    public function test_create_returns_department_instance(): void
    {
        $user = User::factory()->create();
        $branch = Branch::factory()->create();
        $company = Company::factory()->create();

        $department = $this->service->create([
            'branch_id' => $branch->id,
            'company_id' => $company->id,
            'name' => 'Sales',
            'slug' => 'sales',
            'status' => 'active',
        ], $user);

        $this->assertInstanceOf(Department::class, $department);
        $this->assertEquals('Sales', $department->name);
    }

    public function test_update_returns_fresh_department(): void
    {
        $department = Department::factory()->create();

        $result = $this->service->update($department, [
            'name' => 'Updated Department',
            'slug' => $department->slug,
            'status' => 'active',
        ]);

        $this->assertEquals('Updated Department', $result->name);
        $this->assertDatabaseHas('departments', ['id' => $department->id, 'name' => 'Updated Department']);
    }

    public function test_activate_changes_status_to_active(): void
    {
        $department = Department::factory()->inactive()->create();

        $result = $this->service->activate($department);

        $this->assertTrue($result);
        $this->assertDatabaseHas('departments', ['id' => $department->id, 'status' => 'active']);
    }

    public function test_activate_returns_false_if_already_active(): void
    {
        $department = Department::factory()->create();

        $result = $this->service->activate($department);

        $this->assertFalse($result);
    }

    public function test_deactivate_changes_status_to_inactive(): void
    {
        $department = Department::factory()->create();

        $result = $this->service->deactivate($department);

        $this->assertTrue($result);
        $this->assertDatabaseHas('departments', ['id' => $department->id, 'status' => 'inactive']);
    }

    public function test_deactivate_returns_false_if_already_inactive(): void
    {
        $department = Department::factory()->inactive()->create();

        $result = $this->service->deactivate($department);

        $this->assertFalse($result);
    }

    public function test_delete_soft_deletes_department(): void
    {
        $department = Department::factory()->create();

        $result = $this->service->delete($department);

        $this->assertTrue($result['success']);
        $this->assertSoftDeleted('departments', ['id' => $department->id]);
    }

    public function test_restore_restores_soft_deleted_department(): void
    {
        $department = Department::factory()->create();
        $department->delete();

        $result = $this->service->restore($department);

        $this->assertTrue($result);
        $this->assertDatabaseHas('departments', ['id' => $department->id, 'deleted_at' => null]);
    }

    public function test_restore_returns_false_if_not_trashed(): void
    {
        $department = Department::factory()->create();

        $result = $this->service->restore($department);

        $this->assertFalse($result);
    }
}
