<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\Audit\ModelAuditableEvent;
use App\Services\AuditService;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateAuditLogListener implements ShouldQueue
{
    public function __construct(
        private readonly AuditService $auditService,
    ) {}

    public function handle(ModelAuditableEvent $event): void
    {
        try {
            $this->auditService->logEvent(
                $event->event,
                $event->model,
                $event->oldValues,
                $event->newValues,
                $event->metadata,
            );
        } catch (\Exception) {
            // Silently fail - audit logging must never break business logic
        }
    }
}
