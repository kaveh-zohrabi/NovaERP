<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Activity;
use App\Support\BaseService;

class ActivityService extends BaseService
{
    public function create(array $data): Activity
    {
        return Activity::create($data);
    }

    public function update(Activity $activity, array $data): Activity
    {
        $activity->update($data);

        return $activity->fresh();
    }

    public function delete(Activity $activity): array
    {
        $activity->delete();

        return ['success' => true, 'message' => 'Activity deleted successfully.'];
    }

    public function complete(Activity $activity): Activity
    {
        $activity->complete();

        return $activity->fresh();
    }
}
