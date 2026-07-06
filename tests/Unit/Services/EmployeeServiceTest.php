<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use App\Services\EmployeeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeServiceTest extends TestCase
{
    use RefreshDatabase;

    private EmployeeService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(EmployeeService::class);
    }

    public function test_create_returns_employee_instance(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $employee = $this->service->create([
            'company_id' => $company->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'hire_date' => '2024-01-01',
            'status' => 'active',
            'employment_type' => 'full_time',
        ], $user);

        $this->assertInstanceOf(Employee::class, $employee);
        $this->assertEquals('John', $employee->first_name);
    }

    public function test_update_returns_fresh_employee(): void
    {
        $employee = Employee::factory()->create();

        $result = $this->service->update($employee, [
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'email' => $employee->email,
            'hire_date' => $employee->hire_date->format('Y-m-d'),
            'status' => 'active',
            'employment_type' => 'full_time',
        ]);

        $this->assertEquals('Updated', $result->first_name);
        $this->assertDatabaseHas('employees', ['id' => $employee->id, 'first_name' => 'Updated']);
    }

    public function test_terminate_sets_status_and_date(): void
    {
        $employee = Employee::factory()->create();

        $result = $this->service->terminate($employee, '2024-12-31');

        $this->assertEquals('terminated', $result->status->value);
        $this->assertEquals('2024-12-31', $result->termination_date->format('Y-m-d'));
    }

    public function test_terminate_uses_today_if_no_date(): void
    {
        $employee = Employee::factory()->create();

        $result = $this->service->terminate($employee);

        $this->assertEquals('terminated', $result->status->value);
        $this->assertEquals(now()->toDateString(), $result->termination_date->format('Y-m-d'));
    }

    public function test_reactivate_sets_status_active(): void
    {
        $employee = Employee::factory()->terminated()->create();

        $result = $this->service->reactivate($employee);

        $this->assertEquals('active', $result->status->value);
        $this->assertNull($result->termination_date);
    }

    public function test_delete_soft_deletes_employee(): void
    {
        $employee = Employee::factory()->create();

        $result = $this->service->delete($employee);

        $this->assertTrue($result['success']);
        $this->assertSoftDeleted('employees', ['id' => $employee->id]);
    }

    public function test_restore_restores_soft_deleted_employee(): void
    {
        $employee = Employee::factory()->create();
        $employee->delete();

        $result = $this->service->restore($employee);

        $this->assertTrue($result);
        $this->assertDatabaseHas('employees', ['id' => $employee->id, 'deleted_at' => null]);
    }

    public function test_restore_returns_false_if_not_trashed(): void
    {
        $employee = Employee::factory()->create();

        $result = $this->service->restore($employee);

        $this->assertFalse($result);
    }
}
