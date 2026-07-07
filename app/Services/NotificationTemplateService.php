<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\NotificationTemplate;
use App\Support\BaseService;

class NotificationTemplateService extends BaseService
{
    public function create(array $data): NotificationTemplate
    {
        return NotificationTemplate::create($data);
    }

    public function update(NotificationTemplate $template, array $data): NotificationTemplate
    {
        $template->update($data);

        return $template->fresh();
    }

    public function delete(NotificationTemplate $template): array
    {
        $template->delete();

        return ['success' => true, 'message' => 'Template deleted successfully.'];
    }

    public function render(string $name, int $companyId, array $params = []): ?array
    {
        $template = NotificationTemplate::where('name', $name)
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->first();

        if (! $template) {
            return null;
        }

        return $template->render($params);
    }

    public function getActiveTemplates(int $companyId): \Illuminate\Database\Eloquent\Collection
    {
        return NotificationTemplate::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}
