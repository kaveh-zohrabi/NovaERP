<?php

declare(strict_types=1);

namespace App\Events\Audit;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ModelAuditableEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly string $event,
        public readonly mixed $model,
        public readonly ?array $oldValues = null,
        public readonly ?array $newValues = null,
        public readonly ?int $userId = null,
        public readonly array $metadata = [],
    ) {}
}
