<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Opportunity;
use App\Support\BaseService;

class OpportunityService extends BaseService
{
    public function create(array $data): Opportunity
    {
        return Opportunity::create($data);
    }

    public function update(Opportunity $opportunity, array $data): Opportunity
    {
        $opportunity->update($data);

        return $opportunity->fresh();
    }

    public function delete(Opportunity $opportunity): array
    {
        $opportunity->delete();

        return ['success' => true, 'message' => 'Opportunity deleted successfully.'];
    }

    public function moveToStage(Opportunity $opportunity, int $stageId): Opportunity
    {
        if ($opportunity->status !== 'open') {
            throw new \InvalidArgumentException('Only open opportunities can change stages.');
        }

        $opportunity->update([
            'pipeline_stage_id' => $stageId,
        ]);

        return $opportunity->fresh();
    }

    public function markWon(Opportunity $opportunity): Opportunity
    {
        if ($opportunity->status !== 'open') {
            throw new \InvalidArgumentException('Only open opportunities can be marked as won.');
        }

        $opportunity->update([
            'status' => 'won',
            'probability' => 100,
        ]);

        return $opportunity->fresh();
    }

    public function markLost(Opportunity $opportunity, string $reason): Opportunity
    {
        if ($opportunity->status !== 'open') {
            throw new \InvalidArgumentException('Only open opportunities can be marked as lost.');
        }

        $opportunity->update([
            'status' => 'lost',
            'lost_reason' => $reason,
            'probability' => 0,
        ]);

        return $opportunity->fresh();
    }
}
