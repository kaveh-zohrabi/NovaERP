<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Support\BaseDTO;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Data Transfer Object for paginated results.
 *
 * Wraps pagination metadata and results in a typed object.
 *
 * @example
 * $pagination = PaginationDTO::fromLengthAwarePaginator($paginator);
 */
final class PaginationDTO extends BaseDTO
{
    public function __construct(
        public readonly int $currentPage,
        public readonly int $lastPage,
        public readonly int $perPage,
        public readonly int $total,
        public readonly int $from,
        public readonly int $to,
        public readonly array $data,
    ) {}

    /**
     * Create from a LengthAwarePaginator.
     *
     * @param  LengthAwarePaginator  $paginator
     */
    public static function fromPaginator(mixed $paginator): static
    {
        return new self(
            currentPage: $paginator->currentPage(),
            lastPage: $paginator->lastPage(),
            perPage: $paginator->perPage(),
            total: $paginator->total(),
            from: $paginator->firstItem() ?? 0,
            to: $paginator->lastItem() ?? 0,
            data: $paginator->items(),
        );
    }
}
