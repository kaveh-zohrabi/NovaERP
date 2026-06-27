<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * Trait for tracking model changes.
 *
 * Provides methods to log who created, updated, or deleted a model.
 * Requires the model to have `created_by` and `updated_by` columns.
 *
 * @mixin Model
 */
trait Auditable
{
    /**
     * Boot the auditable trait.
     */
    public static function bootAuditable(): void
    {
        static::creating(function (Model $model): void {
            if (auth()->check() && is_null($model->created_by)) {
                $model->created_by = auth()->id();
            }
        });

        static::updating(function (Model $model): void {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }

    /**
     * Get the user who created this record.
     */
    public function createdBy()
    {
        return $this->belongsTo(config('auth.providers.users.model', 'App\\Models\\User'), 'created_by');
    }

    /**
     * Get the user who last updated this record.
     */
    public function updatedBy()
    {
        return $this->belongsTo(config('auth.providers.users.model', 'App\\Models\\User'), 'updated_by');
    }
}
