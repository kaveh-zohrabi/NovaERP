<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Company;
use App\Models\Document;
use App\Models\User;
use App\Services\DocumentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentServiceTest extends TestCase
{
    use RefreshDatabase;

    private DocumentService $service;
    private User $user;
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->service = app(DocumentService::class);
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create();
    }

    public function test_upload_returns_document(): void
    {
        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');

        $doc = $this->service->upload($file, $this->company->id, $this->user->id);

        $this->assertInstanceOf(Document::class, $doc);
        $this->assertEquals('pdf', $doc->extension);
    }

    public function test_duplicate_upload_returns_existing_reference(): void
    {
        $content = 'test content for duplicate check';
        $file = UploadedFile::fake()->createWithContent('dup.pdf', $content);

        $doc1 = $this->service->upload($file, $this->company->id, $this->user->id);
        $file2 = UploadedFile::fake()->createWithContent('dup2.pdf', $content);

        $doc2 = $this->service->upload($file2, $this->company->id, $this->user->id);

        $this->assertEquals($doc1->checksum, $doc2->checksum);
        $this->assertEquals($doc1->path, $doc2->path);
    }

    public function test_rename_updates_original_name(): void
    {
        $doc = Document::factory()->create(['company_id' => $this->company->id, 'uploaded_by' => $this->user->id]);

        $result = $this->service->rename($doc, 'new-name.pdf');

        $this->assertEquals('new-name.pdf', $result->original_name);
    }

    public function test_move_updates_folder(): void
    {
        $folder = \App\Models\Folder::factory()->create(['company_id' => $this->company->id, 'created_by' => $this->user->id]);
        $doc = Document::factory()->create(['company_id' => $this->company->id, 'uploaded_by' => $this->user->id, 'folder_id' => null]);

        $result = $this->service->move($doc, $folder->id);

        $this->assertEquals($folder->id, $result->folder_id);
    }

    public function test_delete_soft_deletes(): void
    {
        $doc = Document::factory()->create(['company_id' => $this->company->id, 'uploaded_by' => $this->user->id]);

        $result = $this->service->delete($doc);

        $this->assertTrue($result['success']);
        $this->assertSoftDeleted('documents', ['id' => $doc->id]);
    }

    public function test_restore_recovers_deleted(): void
    {
        $doc = Document::factory()->create(['company_id' => $this->company->id, 'uploaded_by' => $this->user->id]);
        $doc->delete();

        $result = $this->service->restore($doc);

        $this->assertNull($result->deleted_at);
    }

    public function test_search_returns_paginated(): void
    {
        Document::factory()->count(3)->create(['company_id' => $this->company->id, 'uploaded_by' => $this->user->id]);

        $result = $this->service->search($this->company->id);

        $this->assertEquals(3, $result->total());
    }
}
