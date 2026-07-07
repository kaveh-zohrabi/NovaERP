<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\NotificationPreference;
use App\Support\BaseService;

class NotificationPreferenceService extends BaseService
{
    public function set(int $userId, string $channel, string $notificationType, bool $enabled): NotificationPreference
    {
        return NotificationPreference::updateOrCreate(
            ['user_id' => $userId, 'channel' => $channel, 'notification_type' => $notificationType],
            ['enabled' => $enabled]
        );
    }

    public function isChannelEnabled(int $userId, string $channel, string $notificationType): bool
    {
        $preference = NotificationPreference::where('user_id', $userId)
            ->where('channel', $channel)
            ->where('notification_type', $notificationType)
            ->first();

        if (! $preference) {
            return true;
        }

        return $preference->enabled;
    }

    public function getUserPreferences(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return NotificationPreference::where('user_id', $userId)
            ->orderBy('channel')
            ->orderBy('notification_type')
            ->get();
    }

    public function bulkUpdate(int $userId, array $preferences): int
    {
        $count = 0;

        foreach ($preferences as $pref) {
            $this->set($userId, $pref['channel'], $pref['notification_type'], $pref['enabled']);
            $count++;
        }

        return $count;
    }
}
