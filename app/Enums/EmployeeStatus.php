<?php

declare(strict_types=1);

namespace App\Enums;

enum EmployeeStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';
    case Terminated = 'terminated';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Inactive => 'Inactive',
            self::Suspended => 'Suspended',
            self::Terminated => 'Terminated',
        };
    }

    public function cssClass(): string
    {
        return match ($this) {
            self::Active => 'bg-emerald-100 text-emerald-800',
            self::Inactive => 'bg-gray-100 text-gray-800',
            self::Suspended => 'bg-yellow-100 text-yellow-800',
            self::Terminated => 'bg-red-100 text-red-800',
        };
    }
}
