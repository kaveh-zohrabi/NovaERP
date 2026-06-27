<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Trait for UUID primary keys.
 *
 * Adds a UUID column as the primary key and automatically
 * generates UUIDs when creating new models.
 *
 * @mixin Model
 */
trait HasUuid
{
    /**
     * Boot the HasUuid trait.
     */
    public static function bootHasUuid(): void
    {
        static::creating(function (Model $model): void {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the primary key type for the model.
     */
    public function getKeyType(): string
    {
        return 'string';
    }

    /**
     * Get whether the model IDs are incrementing.
     */
    public function getIncrementing(): bool
    {
        return false;
    }
}
