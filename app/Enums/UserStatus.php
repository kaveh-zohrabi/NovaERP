<?php

declare(strict_types=1);

namespace App\Enums;

enum UserStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Inactive => 'Inactive',
            self::Suspended => 'Suspended',
        };
    }

    public function cssClass(): string
    {
        return match ($this) {
            self::Active => 'bg-emerald-100 text-emerald-800',
            self::Inactive => 'bg-gray-100 text-gray-800',
            self::Suspended => 'bg-red-100 text-red-800',
        };
    }

    public function isAccessible(): bool
    {
        return $this === self::Active;
    }
}
