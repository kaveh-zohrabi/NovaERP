<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Abstract base class for Data Transfer Objects.
 *
 * DTOs are immutable objects that carry data between layers.
 * They should contain no business logic, only data and conversion methods.
 *
 * @example
 * class StockTransferDTO extends BaseDTO
 * {
 *     public function __construct(
 *         public readonly int $productId,
 *         public readonly int $quantity,
 *         public readonly ?string $notes = null,
 *     ) {}
 * }
 */
abstract class BaseDTO
{
    /**
     * Create a DTO from an array of data.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static
    {
        return new static(...$data);
    }

    /**
     * Create a DTO from a JSON string.
     */
    public static function fromJson(string $json): static
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return static::fromArray($data);
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * Convert the DTO to a JSON string.
     */
    public function toJson(int $options = JSON_THROW_ON_ERROR): string
    {
        return json_encode($this->toArray(), $options);
    }
}
