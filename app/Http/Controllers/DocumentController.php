<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\DocumentService;
use App\Services\FilePreviewService;
use App\Services\StorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function __construct(
        private readonly DocumentService $documentService,
        private readonly StorageService $storageService,
        private readonly FilePreviewService $previewService,
    ) {}

    public function index(Request $request): View
    {
        $documents = $this->documentService->search(
            $request->user()->company_id ?? 1,
            $request->only(['search', 'folder_id', 'mime_type', 'extension']),
        );

        return view('documents.index', compact('documents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'files' => ['required', 'array', 'max:10'],
            'files.*' => ['file', 'max:10240', 'mimes:pdf,docx,xlsx,csv,txt,jpg,jpeg,png,webp'],
            'folder_id' => ['nullable', 'exists:folders,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'documentable_type' => ['nullable', 'string'],
            'documentable_id' => ['nullable', 'integer'],
        ]);

        foreach ($request->file('files') as $file) {
            $this->documentService->upload(
                $file,
                $request->user()->company_id ?? 1,
                $request->user()->id,
                $request->documentable_type,
                $request->documentable_id,
                $request->folder_id,
                $request->description,
            );
        }

        return back()->with('success', 'Files uploaded successfully.');
    }

    public function show(Document $document): View
    {
        $previewContent = null;
        $previewType = null;

        if ($this->previewService->canPreview($document)) {
            $previewContent = $this->previewService->getPreviewContent($document);
            $previewType = $this->previewService->getPreviewType($document);
        }

        return view('documents.show', compact('document', 'previewContent', 'previewType'));
    }

    public function download(Document $document): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return $this->documentService->download($document);
    }

    public function rename(Request $request, Document $document): RedirectResponse
    {
        $request->validate([
            'original_name' => ['required', 'string', 'max:255'],
        ]);

        $this->documentService->rename($document, $request->original_name);

        return back()->with('success', 'Document renamed successfully.');
    }

    public function move(Request $request, Document $document): RedirectResponse
    {
        $request->validate([
            'folder_id' => ['nullable', 'exists:folders,id'],
        ]);

        $this->documentService->move($document, $request->folder_id);

        return back()->with('success', 'Document moved successfully.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        $this->documentService->delete($document);

        return back()->with('success', 'Document moved to trash.');
    }

    public function restore(Document $document): RedirectResponse
    {
        $this->documentService->restore($document);

        return back()->with('success', 'Document restored successfully.');
    }

    public function forceDelete(Document $document): RedirectResponse
    {
        $this->documentService->forceDelete($document);

        return back()->with('success', 'Document permanently deleted.');
    }

    public function trash(Request $request): View
    {
        $documents = Document::onlyTrashed()
            ->where('company_id', $request->user()->company_id ?? 1)
            ->with('uploader')
            ->latest()
            ->paginate(15);

        return view('documents.trash', compact('documents'));
    }
}
