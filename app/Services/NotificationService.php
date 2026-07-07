<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\UserNotification;
use App\Support\BaseService;
use Illuminate\Support\Facades\DB;

class NotificationService extends BaseService
{
    public function create(array $data): UserNotification
    {
        if ($this->isDuplicate($data)) {
            return $this->findExisting($data);
        }

        return UserNotification::create($data);
    }

    public function createBulk(array $userIds, array $data): int
    {
        $records = collect($userIds)->map(fn (int $userId) => array_merge($data, [
            'user_id' => $userId,
            'status' => 'unread',
        ]))->toArray();

        return UserNotification::insert($records);
    }

    public function markAsRead(UserNotification $notification): UserNotification
    {
        $notification->markAsRead();

        return $notification->fresh();
    }

    public function markAllAsRead(int $userId, int $companyId): int
    {
        return UserNotification::where('user_id', $userId)
            ->where('company_id', $companyId)
            ->where('status', 'unread')
            ->update(['status' => 'read', 'read_at' => now()]);
    }

    public function delete(UserNotification $notification): array
    {
        $notification->delete();

        return ['success' => true, 'message' => 'Notification deleted.'];
    }

    public function archive(UserNotification $notification): UserNotification
    {
        $notification->update(['status' => 'archived']);

        return $notification->fresh();
    }

    public function getUnreadCount(int $userId): int
    {
        return UserNotification::where('user_id', $userId)
            ->where('status', 'unread')
            ->count();
    }

    public function getUserNotifications(int $userId, int $companyId, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = UserNotification::where('user_id', $userId)
            ->where('company_id', $companyId);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        return $query->latest()->paginate(15)->withQueryString();
    }

    private function isDuplicate(array $data): bool
    {
        return UserNotification::where('user_id', $data['user_id'] ?? 0)
            ->where('type', $data['type'] ?? '')
            ->where('title', $data['title'] ?? '')
            ->where('created_at', '>=', now()->subMinutes(5))
            ->exists();
    }

    private function findExisting(array $data): UserNotification
    {
        return UserNotification::where('user_id', $data['user_id'] ?? 0)
            ->where('type', $data['type'] ?? '')
            ->where('title', $data['title'] ?? '')
            ->latest()
            ->first();
    }
}
