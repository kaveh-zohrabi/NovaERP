<?php

declare(strict_types=1);

namespace Tests\Feature\Documents;

use App\Models\Company;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FolderTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();
        $this->user = User::factory()->create();
    }

    public function test_index_displays_folders(): void
    {
        $response = $this->actingAs($this->user)->get(route('folders.index'));

        $response->assertOk();
        $response->assertSee('Folders');
    }

    public function test_folder_can_be_created(): void
    {
        $response = $this->actingAs($this->user)->post(route('folders.store'), [
            'name' => 'Invoices',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('folders', ['name' => 'Invoices']);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('folders.store'), []);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_folder_can_be_updated(): void
    {
        $folder = Folder::factory()->create(['company_id' => $this->company->id, 'created_by' => $this->user->id]);

        $response = $this->actingAs($this->user)->patch(route('folders.update', $folder), [
            'name' => 'Updated Folder',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('folders', ['id' => $folder->id, 'name' => 'Updated Folder']);
    }

    public function test_folder_can_be_deleted(): void
    {
        $folder = Folder::factory()->create(['company_id' => $this->company->id, 'created_by' => $this->user->id]);

        $response = $this->actingAs($this->user)->delete(route('folders.destroy', $folder));

        $response->assertRedirect();
        $this->assertSoftDeleted('folders', ['id' => $folder->id]);
    }
}
