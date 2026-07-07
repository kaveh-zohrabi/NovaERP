<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Document;
use App\Support\BaseService;
use Illuminate\Support\Facades\Storage;

class StorageService extends BaseService
{
    public function getStoragePath(Document $document): string
    {
        return Storage::disk($document->disk)->path($document->path);
    }

    public function getTemporaryUrl(Document $document, int $expirationMinutes = 60): string
    {
        if (Storage::disk($document->disk)->providesTemporaryUrl()) {
            return Storage::disk($document->disk)->temporaryUrl($document->path, now()->addMinutes($expirationMinutes));
        }

        return route('documents.download', $document);
    }

    public function getPublicUrl(Document $document): ?string
    {
        if (! $document->is_public) {
            return null;
        }

        return Storage::disk($document->disk)->url($document->path);
    }

    public function fileExists(Document $document): bool
    {
        return Storage::disk($document->disk)->exists($document->path);
    }

    public function getDiskUsage(int $companyId): array
    {
        $totalSize = Document::where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->sum('file_size');

        return [
            'total_size' => $totalSize,
            'formatted_size' => $this->formatBytes($totalSize),
            'total_files' => Document::where('company_id', $companyId)->whereNull('deleted_at')->count(),
        ];
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return round($bytes / 1073741824, 2).' GB';
        }

        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2).' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 2).' KB';
        }

        return $bytes.' B';
    }
}
