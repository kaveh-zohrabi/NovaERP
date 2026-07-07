<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Services\FolderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FolderController extends Controller
{
    public function __construct(
        private readonly FolderService $folderService,
    ) {}

    public function index(Request $request): View
    {
        $folders = $this->folderService->getTree($request->user()->company_id ?? 1);

        return view('documents.folders.index', compact('folders'));
    }

    public function show(Folder $folder): View
    {
        $folder->loadCount('documents');
        $children = $this->folderService->getChildren($folder);
        $documents = $folder->documents()->with('uploader')->latest()->paginate(15);

        return view('documents.folders.show', compact('folder', 'children', 'documents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'exists:folders,id'],
        ]);

        $validated['company_id'] = $request->user()->company_id ?? 1;
        $validated['created_by'] = $request->user()->id;

        $this->folderService->create($validated);

        return back()->with('success', 'Folder created successfully.');
    }

    public function update(Request $request, Folder $folder): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->folderService->update($folder, $validated);

        return back()->with('success', 'Folder updated successfully.');
    }

    public function destroy(Folder $folder): RedirectResponse
    {
        $this->folderService->delete($folder);

        return back()->with('success', 'Folder deleted successfully.');
    }
}
