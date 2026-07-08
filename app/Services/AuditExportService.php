<?php

declare(strict_types=1);

namespace App\Services;

use App\Support\BaseService;

class AuditExportService extends BaseService
{
    public function __construct(
        private readonly AuditService $auditService,
        private readonly ExportService $exportService,
    ) {}

    public function exportAuditLogs(int $companyId, array $filters = [], string $format = 'csv'): string
    {
        $query = \App\Models\AuditLog::where('company_id', $companyId)
            ->with('user');

        $this->applyFilters($query, $filters);

        $logs = $query->orderBy('created_at', 'desc')->limit(10000)->get()->map(fn ($log) => [
            'id' => $log->id,
            'event' => $log->event,
            'auditable_type' => class_basename($log->auditable_type ?? ''),
            'auditable_id' => $log->auditable_id,
            'user' => $log->user?->name ?? 'System',
            'ip_address' => $log->ip_address ?? '',
            'old_values' => json_encode($log->old_values),
            'new_values' => json_encode($log->new_values),
            'created_at' => $log->created_at->format('Y-m-d H:i:s'),
        ])->toArray();

        $filename = 'audit-logs-'.now()->format('Y-m-d-His');

        if ($format === 'pdf') {
            return $this->exportService->exportPdf($logs, $filename);
        }

        return $this->exportService->exportCsv($logs, $filename);
    }

    private function applyFilters($query, array $filters): void
    {
        if (! empty($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        if (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (! empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }
    }
}
