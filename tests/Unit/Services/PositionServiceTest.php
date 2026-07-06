<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use App\Services\PositionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PositionServiceTest extends TestCase
{
    use RefreshDatabase;

    private PositionService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(PositionService::class);
    }

    public function test_create_returns_position_instance(): void
    {
        $user = User::factory()->create();
        $department = Department::factory()->create();

        $position = $this->service->create([
            'department_id' => $department->id,
            'company_id' => $department->company_id,
            'name' => 'Sales Manager',
            'status' => 'active',
        ], $user);

        $this->assertInstanceOf(Position::class, $position);
        $this->assertEquals('Sales Manager', $position->name);
    }

    public function test_update_returns_fresh_position(): void
    {
        $position = Position::factory()->create();

        $result = $this->service->update($position, [
            'name' => 'Updated Position',
            'status' => 'active',
        ]);

        $this->assertEquals('Updated Position', $result->name);
        $this->assertDatabaseHas('positions', ['id' => $position->id, 'name' => 'Updated Position']);
    }

    public function test_activate_changes_status_to_active(): void
    {
        $position = Position::factory()->inactive()->create();

        $result = $this->service->activate($position);

        $this->assertTrue($result);
        $this->assertDatabaseHas('positions', ['id' => $position->id, 'status' => 'active']);
    }

    public function test_activate_returns_false_if_already_active(): void
    {
        $position = Position::factory()->create();

        $result = $this->service->activate($position);

        $this->assertFalse($result);
    }

    public function test_deactivate_changes_status_to_inactive(): void
    {
        $position = Position::factory()->create();

        $result = $this->service->deactivate($position);

        $this->assertTrue($result);
        $this->assertDatabaseHas('positions', ['id' => $position->id, 'status' => 'inactive']);
    }

    public function test_deactivate_returns_false_if_already_inactive(): void
    {
        $position = Position::factory()->inactive()->create();

        $result = $this->service->deactivate($position);

        $this->assertFalse($result);
    }

    public function test_delete_soft_deletes_position(): void
    {
        $position = Position::factory()->create();

        $result = $this->service->delete($position);

        $this->assertTrue($result['success']);
        $this->assertSoftDeleted('positions', ['id' => $position->id]);
    }

    public function test_restore_restores_soft_deleted_position(): void
    {
        $position = Position::factory()->create();
        $position->delete();

        $result = $this->service->restore($position);

        $this->assertTrue($result);
        $this->assertDatabaseHas('positions', ['id' => $position->id, 'deleted_at' => null]);
    }

    public function test_restore_returns_false_if_not_trashed(): void
    {
        $position = Position::factory()->create();

        $result = $this->service->restore($position);

        $this->assertFalse($result);
    }
}
