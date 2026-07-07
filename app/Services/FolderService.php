<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Folder;
use App\Support\BaseService;

class FolderService extends BaseService
{
    public function create(array $data): Folder
    {
        return Folder::create($data);
    }

    public function update(Folder $folder, array $data): Folder
    {
        $folder->update($data);

        return $folder->fresh();
    }

    public function delete(Folder $folder): array
    {
        $folder->delete();

        return ['success' => true, 'message' => 'Folder deleted successfully.'];
    }

    public function restore(Folder $folder): Folder
    {
        $folder->restore();

        return $folder->fresh();
    }

    public function getTree(int $companyId): array
    {
        return Folder::where('company_id', $companyId)
            ->whereNull('parent_id')
            ->with(['children' => function ($q) {
                $q->withCount('documents');
            }])
            ->withCount('documents')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function getChildren(Folder $folder): \Illuminate\Database\Eloquent\Collection
    {
        return $folder->children()
            ->withCount('documents')
            ->orderBy('name')
            ->get();
    }
}
