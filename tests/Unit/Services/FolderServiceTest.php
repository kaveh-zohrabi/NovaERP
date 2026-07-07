<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Company;
use App\Models\Folder;
use App\Models\User;
use App\Services\FolderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FolderServiceTest extends TestCase
{
    use RefreshDatabase;

    private FolderService $service;
    private Company $company;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(FolderService::class);
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create();
    }

    public function test_create_returns_folder(): void
    {
        $folder = $this->service->create([
            'company_id' => $this->company->id,
            'created_by' => $this->user->id,
            'name' => 'Test Folder',
        ]);

        $this->assertInstanceOf(Folder::class, $folder);
        $this->assertEquals('Test Folder', $folder->name);
    }

    public function test_update_changes_name(): void
    {
        $folder = Folder::factory()->create(['company_id' => $this->company->id, 'created_by' => $this->user->id]);

        $result = $this->service->update($folder, ['name' => 'Updated']);

        $this->assertEquals('Updated', $result->name);
    }

    public function test_delete_soft_deletes(): void
    {
        $folder = Folder::factory()->create(['company_id' => $this->company->id, 'created_by' => $this->user->id]);

        $result = $this->service->delete($folder);

        $this->assertTrue($result['success']);
        $this->assertSoftDeleted('folders', ['id' => $folder->id]);
    }

    public function test_get_tree_returns_root_folders(): void
    {
        Folder::factory()->create(['company_id' => $this->company->id, 'created_by' => $this->user->id, 'name' => 'A']);
        Folder::factory()->create(['company_id' => $this->company->id, 'created_by' => $this->user->id, 'name' => 'B']);

        $tree = $this->service->getTree($this->company->id);

        $this->assertCount(2, $tree);
    }
}
