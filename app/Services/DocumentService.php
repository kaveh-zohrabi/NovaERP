<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Document;
use App\Models\Folder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentService extends BaseService
{
    public function upload(
        UploadedFile $file,
        int $companyId,
        int $uploadedBy,
        ?string $documentableType = null,
        ?int $documentableId = null,
        ?int $folderId = null,
        ?string $description = null,
        bool $isPublic = false,
        string $disk = 'local',
    ): Document {
        $checksum = hash_file('sha256', $file->getRealPath());

        $existing = Document::where('company_id', $companyId)
            ->where('checksum', $checksum)
            ->whereNull('deleted_at')
            ->first();

        if ($existing) {
            return $this->createReference($existing, $companyId, $uploadedBy, $documentableType, $documentableId, $folderId, $description);
        }

        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        $fileName = Str::uuid().'.'.$extension;
        $folder = $folderId ? 'folders/'.$folderId : 'general';
        $path = $file->storeAs("documents/{$folder}", $fileName, $disk);

        return Document::create([
            'company_id' => $companyId,
            'uploaded_by' => $uploadedBy,
            'documentable_type' => $documentableType,
            'documentable_id' => $documentableId,
            'folder_id' => $folderId,
            'file_name' => $fileName,
            'original_name' => $originalName,
            'mime_type' => $file->getMimeType(),
            'extension' => $extension,
            'file_size' => $file->getSize(),
            'disk' => $disk,
            'path' => $path,
            'checksum' => $checksum,
            'description' => $description,
            'is_public' => $isPublic,
        ]);
    }

    private function createReference(
        Document $original,
        int $companyId,
        int $uploadedBy,
        ?string $documentableType,
        ?int $documentableId,
        ?int $folderId,
        ?string $description,
    ): Document {
        return Document::create([
            'company_id' => $companyId,
            'uploaded_by' => $uploadedBy,
            'documentable_type' => $documentableType,
            'documentable_id' => $documentableId,
            'folder_id' => $folderId,
            'file_name' => $original->file_name,
            'original_name' => $original->original_name,
            'mime_type' => $original->mime_type,
            'extension' => $original->extension,
            'file_size' => $original->file_size,
            'disk' => $original->disk,
            'path' => $original->path,
            'checksum' => $original->checksum,
            'description' => $description,
            'is_public' => false,
        ]);
    }

    public function download(Document $document): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return Storage::disk($document->disk)->download($document->path, $document->original_name);
    }

    public function rename(Document $document, string $newName): Document
    {
        $document->update(['original_name' => $newName]);

        return $document->fresh();
    }

    public function move(Document $document, ?int $folderId): Document
    {
        $document->update(['folder_id' => $folderId]);

        return $document->fresh();
    }

    public function delete(Document $document): array
    {
        $document->delete();

        return ['success' => true, 'message' => 'Document moved to trash.'];
    }

    public function restore(Document $document): Document
    {
        $document->restore();

        return $document->fresh();
    }

    public function forceDelete(Document $document): array
    {
        Storage::disk($document->disk)->delete($document->path);
        $document->forceDelete();

        return ['success' => true, 'message' => 'Document permanently deleted.'];
    }

    public function search(int $companyId, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Document::where('company_id', $companyId)
            ->with('uploader', 'folder');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('original_name', 'like', "%{$search}%")
                    ->orWhere('file_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['folder_id'])) {
            $query->where('folder_id', $filters['folder_id']);
        }

        if (! empty($filters['mime_type'])) {
            $query->where('mime_type', 'like', $filters['mime_type'].'%');
        }

        if (! empty($filters['extension'])) {
            $query->where('extension', $filters['extension']);
        }

        return $query->latest()->paginate(15)->withQueryString();
    }
}
