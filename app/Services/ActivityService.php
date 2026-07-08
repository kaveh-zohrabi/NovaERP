<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ActivityLog;
use App\Support\BaseService;
use Illuminate\Support\Facades\Auth;

class ActivityService extends BaseService
{
    public function log(array $data): ActivityLog
    {
        return ActivityLog::create(array_merge($data, [
            'company_id' => $data['company_id'] ?? (Auth::check() ? Auth::user()->company_id : 1),
            'user_id' => $data['user_id'] ?? Auth::id(),
            'created_at' => $data['created_at'] ?? now(),
        ]));
    }

    public function record(string $type, string $description, $subject = null, array $metadata = []): ActivityLog
    {
        return $this->log([
            'activity_type' => $type,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->id,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    public function getForSubject($subject): \Illuminate\Database\Eloquent\Collection
    {
        return ActivityLog::where('subject_type', get_class($subject))
            ->where('subject_id', $subject->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getUserActivity(int $userId, int $companyId, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = ActivityLog::where('user_id', $userId)
            ->where('company_id', $companyId)
            ->with('user');

        if (! empty($filters['activity_type'])) {
            $query->where('activity_type', $filters['activity_type']);
        }

        if (! empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
    }

    public function query(int $companyId, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = ActivityLog::where('company_id', $companyId)
            ->with('user');

        if (! empty($filters['activity_type'])) {
            $query->where('activity_type', $filters['activity_type']);
        }

        if (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (! empty($filters['subject_type'])) {
            $query->where('subject_type', $filters['subject_type']);
        }

        if (! empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('description', 'like', "%{$search}%");
        }

        return $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
    }
}
