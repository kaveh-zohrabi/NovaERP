<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use App\Support\BaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuditService extends BaseService
{
    public function log(array $data): AuditLog
    {
        $request = request();

        return AuditLog::create(array_merge($data, [
            'company_id' => $data['company_id'] ?? (Auth::check() ? Auth::user()->company_id : 1),
            'user_id' => $data['user_id'] ?? Auth::id(),
            'ip_address' => $data['ip_address'] ?? ($request?->ip()),
            'user_agent' => $data['user_agent'] ?? ($request?->userAgent()),
            'request_id' => $data['request_id'] ?? ($request?->header('X-Request-Id', Str::uuid()->toString())),
            'created_at' => $data['created_at'] ?? now(),
        ]));
    }

    public function logEvent(string $event, $model = null, array $oldValues = null, array $newValues = null, array $metadata = []): AuditLog
    {
        return $this->log([
            'event' => $event,
            'auditable_type' => $model ? get_class($model) : null,
            'auditable_id' => $model?->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'metadata' => $metadata,
        ]);
    }

    public function getHistory($model): \Illuminate\Database\Eloquent\Collection
    {
        return AuditLog::where('auditable_type', get_class($model))
            ->where('auditable_id', $model->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function query(int $companyId, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = AuditLog::where('company_id', $companyId)
            ->with('user');

        if (! empty($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        if (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (! empty($filters['auditable_type'])) {
            $query->where('auditable_type', $filters['auditable_type']);
        }

        if (! empty($filters['auditable_id'])) {
            $query->where('auditable_id', $filters['auditable_id']);
        }

        if (! empty($filters['ip_address'])) {
            $query->where('ip_address', $filters['ip_address']);
        }

        if (! empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('event', 'like', "%{$search}%")
                    ->orWhere('auditable_type', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
    }
}
