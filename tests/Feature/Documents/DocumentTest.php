<?php

declare(strict_types=1);

namespace Tests\Feature\Documents;

use App\Models\Company;
use App\Models\Document;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create();
    }

    public function test_index_displays_documents(): void
    {
        $response = $this->actingAs($this->user)->get(route('documents.index'));

        $response->assertOk();
        $response->assertSee('Documents');
    }

    public function test_document_can_be_uploaded(): void
    {
        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->user)->post(route('documents.store'), [
            'files' => [$file],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('documents', ['original_name' => 'test.pdf']);
    }

    public function test_store_validates_file_type(): void
    {
        $file = UploadedFile::fake()->create('malware.exe', 100, 'application/x-executable');

        $response = $this->actingAs($this->user)->post(route('documents.store'), [
            'files' => [$file],
        ]);

        $response->assertSessionHasErrors('files.0');
    }

    public function test_document_can_be_downloaded(): void
    {
        $document = Document::factory()->create([
            'company_id' => $this->company->id,
            'uploaded_by' => $this->user->id,
        ]);

        Storage::disk('local')->put($document->path, 'test content');

        $response = $this->actingAs($this->user)->get(route('documents.download', $document));

        $response->assertOk();
    }

    public function test_document_can_be_deleted(): void
    {
        $document = Document::factory()->create([
            'company_id' => $this->company->id,
            'uploaded_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('documents.destroy', $document));

        $response->assertRedirect();
        $this->assertSoftDeleted('documents', ['id' => $document->id]);
    }

    public function test_document_can_be_restored(): void
    {
        $document = Document::factory()->create([
            'company_id' => $this->company->id,
            'uploaded_by' => $this->user->id,
        ]);
        $document->delete();

        $response = $this->actingAs($this->user)->patch(route('documents.restore', $document));

        $response->assertRedirect();
        $this->assertDatabaseHas('documents', ['id' => $document->id, 'deleted_at' => null]);
    }

    public function test_document_can_be_renamed(): void
    {
        $document = Document::factory()->create([
            'company_id' => $this->company->id,
            'uploaded_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->patch(route('documents.rename', $document), [
            'original_name' => 'renamed.pdf',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('documents', ['id' => $document->id, 'original_name' => 'renamed.pdf']);
    }

    public function test_document_can_be_moved(): void
    {
        $folder = Folder::factory()->create(['company_id' => $this->company->id, 'created_by' => $this->user->id]);
        $document = Document::factory()->create([
            'company_id' => $this->company->id,
            'uploaded_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->patch(route('documents.move', $document), [
            'folder_id' => $folder->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('documents', ['id' => $document->id, 'folder_id' => $folder->id]);
    }

    public function test_search_filters_documents(): void
    {
        Document::factory()->create([
            'company_id' => $this->company->id,
            'uploaded_by' => $this->user->id,
            'original_name' => 'invoice-2026.pdf',
        ]);

        $response = $this->actingAs($this->user)->get(route('documents.index', ['search' => 'invoice']));

        $response->assertOk();
        $response->assertSee('invoice-2026.pdf');
    }

    public function test_trash_shows_deleted_documents(): void
    {
        $document = Document::factory()->create([
            'company_id' => $this->company->id,
            'uploaded_by' => $this->user->id,
        ]);
        $document->delete();

        $response = $this->actingAs($this->user)->get(route('documents.trash'));

        $response->assertOk();
        $response->assertSee($document->original_name);
    }
}
