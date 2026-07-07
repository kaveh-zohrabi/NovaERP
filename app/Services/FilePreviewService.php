<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Document;
use App\Support\BaseService;
use Illuminate\Support\Facades\Storage;

class FilePreviewService extends BaseService
{
    private const PREVIEWABLE_MIMES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'application/pdf',
        'text/plain',
        'text/csv',
    ];

    private const IMAGE_MIMES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    public function canPreview(Document $document): bool
    {
        return in_array($document->mime_type, self::PREVIEWABLE_MIMES);
    }

    public function getPreviewContent(Document $document): ?string
    {
        if (! $this->canPreview($document)) {
            return null;
        }

        $content = Storage::disk($document->disk)->get($document->path);

        if ($document->mime_type === 'text/plain' || $document->mime_type === 'text/csv') {
            return htmlspecialchars($content);
        }

        return base64_encode($content);
    }

    public function getPreviewType(Document $document): ?string
    {
        if (in_array($document->mime_type, self::IMAGE_MIMES)) {
            return 'image';
        }

        if ($document->mime_type === 'application/pdf') {
            return 'pdf';
        }

        if (in_array($document->mime_type, ['text/plain', 'text/csv'])) {
            return 'text';
        }

        return null;
    }

    public function getSupportedExtensions(): array
    {
        return [
            'pdf', 'docx', 'xlsx', 'csv', 'txt',
            'jpg', 'jpeg', 'png', 'webp',
        ];
    }

    public function getMaxFileSize(): int
    {
        return 10 * 1024 * 1024;
    }
}
