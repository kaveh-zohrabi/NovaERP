<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use App\Services\BranchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchServiceTest extends TestCase
{
    use RefreshDatabase;

    private BranchService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(BranchService::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function test_create_returns_branch_instance(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $branch = $this->service->create([
            'company_id' => $company->id,
            'name' => 'New York Office',
            'slug' => 'new-york-office',
            'status' => 'active',
        ], $user);

        $this->assertInstanceOf(Branch::class, $branch);
        $this->assertEquals('New York Office', $branch->name);
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function test_update_returns_fresh_branch(): void
    {
        $branch = Branch::factory()->create();

        $result = $this->service->update($branch, [
            'name' => 'Updated Branch',
            'slug' => $branch->slug,
            'status' => 'active',
        ]);

        $this->assertEquals('Updated Branch', $result->name);
        $this->assertDatabaseHas('branches', ['id' => $branch->id, 'name' => 'Updated Branch']);
    }

    /*
    |--------------------------------------------------------------------------
    | Activate / Deactivate
    |--------------------------------------------------------------------------
    */

    public function test_activate_changes_status_to_active(): void
    {
        $branch = Branch::factory()->inactive()->create();

        $result = $this->service->activate($branch);

        $this->assertTrue($result);
        $this->assertDatabaseHas('branches', ['id' => $branch->id, 'status' => 'active']);
    }

    public function test_activate_returns_false_if_already_active(): void
    {
        $branch = Branch::factory()->create();

        $result = $this->service->activate($branch);

        $this->assertFalse($result);
    }

    public function test_deactivate_changes_status_to_inactive(): void
    {
        $branch = Branch::factory()->create();

        $result = $this->service->deactivate($branch);

        $this->assertTrue($result);
        $this->assertDatabaseHas('branches', ['id' => $branch->id, 'status' => 'inactive']);
    }

    public function test_deactivate_returns_false_if_already_inactive(): void
    {
        $branch = Branch::factory()->inactive()->create();

        $result = $this->service->deactivate($branch);

        $this->assertFalse($result);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function test_delete_soft_deletes_branch(): void
    {
        $branch = Branch::factory()->create();

        $result = $this->service->delete($branch);

        $this->assertTrue($result['success']);
        $this->assertSoftDeleted('branches', ['id' => $branch->id]);
    }

    /*
    |--------------------------------------------------------------------------
    | Restore
    |--------------------------------------------------------------------------
    */

    public function test_restore_restores_soft_deleted_branch(): void
    {
        $branch = Branch::factory()->create();
        $branch->delete();

        $result = $this->service->restore($branch);

        $this->assertTrue($result);
        $this->assertDatabaseHas('branches', ['id' => $branch->id, 'deleted_at' => null]);
    }

    public function test_restore_returns_false_if_not_trashed(): void
    {
        $branch = Branch::factory()->create();

        $result = $this->service->restore($branch);

        $this->assertFalse($result);
    }
}
