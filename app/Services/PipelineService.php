<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Support\BaseService;

class PipelineService extends BaseService
{
    public function create(array $data): Pipeline
    {
        return Pipeline::create($data);
    }

    public function update(Pipeline $pipeline, array $data): Pipeline
    {
        $pipeline->update($data);

        return $pipeline->fresh();
    }

    public function delete(Pipeline $pipeline): array
    {
        $pipeline->delete();

        return ['success' => true, 'message' => 'Pipeline deleted successfully.'];
    }

    public function addStage(Pipeline $pipeline, array $data): PipelineStage
    {
        $maxOrder = $pipeline->stages()->max('sort_order') ?? 0;

        return $pipeline->stages()->create([
            'name' => $data['name'],
            'probability' => $data['probability'] ?? 0,
            'sort_order' => $maxOrder + 1,
        ]);
    }

    public function updateStage(PipelineStage $stage, array $data): PipelineStage
    {
        $stage->update($data);

        return $stage->fresh();
    }

    public function removeStage(PipelineStage $stage): array
    {
        $stage->delete();

        return ['success' => true, 'message' => 'Stage removed successfully.'];
    }
}
