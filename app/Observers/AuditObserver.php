<?php

declare(strict_types=1);

namespace App\Observers;

use App\Events\Audit\ModelAuditableEvent;
use Illuminate\Database\Eloquent\Model;

class AuditObserver
{
    public function created(Model $model): void
    {
        try {
            ModelAuditableEvent::dispatch('created', $model, null, $model->toArray());
        } catch (\Exception) {
        }
    }

    public function updated(Model $model): void
    {
        try {
            $old = $model->getOriginal();
            $new = $model->getAttributes();

            ModelAuditableEvent::dispatch('updated', $model, $old, $new);
        } catch (\Exception) {
        }
    }

    public function deleted(Model $model): void
    {
        try {
            ModelAuditableEvent::dispatch('deleted', $model, $model->toArray(), null);
        } catch (\Exception) {
        }
    }

    public function restored(Model $model): void
    {
        try {
            ModelAuditableEvent::dispatch('restored', $model, null, $model->toArray());
        } catch (\Exception) {
        }
    }
}
