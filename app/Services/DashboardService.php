<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Dashboard;
use App\Models\DashboardWidget;
use App\Support\BaseService;

class DashboardService extends BaseService
{
    public function create(array $data): Dashboard
    {
        return Dashboard::create($data);
    }

    public function update(Dashboard $dashboard, array $data): Dashboard
    {
        $dashboard->update($data);

        return $dashboard->fresh();
    }

    public function delete(Dashboard $dashboard): array
    {
        $dashboard->delete();

        return ['success' => true, 'message' => 'Dashboard deleted successfully.'];
    }

    public function addWidget(Dashboard $dashboard, array $data): DashboardWidget
    {
        $maxPosition = $dashboard->widgets()->max('position') ?? 0;

        return $dashboard->widgets()->create([
            'widget_type' => $data['widget_type'],
            'configuration' => $data['configuration'] ?? null,
            'position' => $maxPosition + 1,
            'size' => $data['size'] ?? 'medium',
        ]);
    }

    public function removeWidget(DashboardWidget $widget): array
    {
        $widget->delete();

        return ['success' => true, 'message' => 'Widget removed successfully.'];
    }

    public function getUserDashboard(int $companyId, int $userId): ?Dashboard
    {
        return Dashboard::where('company_id', $companyId)
            ->where('created_by', $userId)
            ->with('widgets')
            ->first();
    }
}
