<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * General-purpose status enum for shared use across domains.
 *
 * For domain-specific statuses, create enums within the domain folder
 * (e.g., Core\Inventory\Enums\StockStatus).
 */
enum Status: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Draft = 'draft';

    /**
     * Get the label for display purposes.
     */
    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Inactive => 'Inactive',
            self::Pending => 'Pending',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::Draft => 'Draft',
        };
    }

    /**
     * Get the CSS class for status badges.
     */
    public function cssClass(): string
    {
        return match ($this) {
            self::Active => 'badge-success',
            self::Inactive => 'badge-secondary',
            self::Pending => 'badge-warning',
            self::Approved => 'badge-success',
            self::Rejected => 'badge-danger',
            self::Draft => 'badge-info',
        };
    }
}
