<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

/**
 * Base exception for all domain-specific errors.
 *
 * This exception should be extended by domain-specific exceptions
 * to provide meaningful error messages and context.
 *
 * @example
 * class InsufficientStockException extends DomainException
 * {
 *     public function __construct(int $requested, int $available)
 *     {
 *         parent::__construct(
 *             message: "Insufficient stock: requested {$requested}, available {$available}",
 *             code: 'INSUFFICIENT_STOCK',
 *             context: ['requested' => $requested, 'available' => $available]
 *         );
 *     }
 * }
 */
class DomainException extends RuntimeException
{
    /**
     * @var array<string, mixed> Additional context for debugging
     */
    protected array $context = [];

    /**
     * @param  string  $message  Human-readable error message
     * @param  string  $code  Machine-readable error code
     * @param  array<string, mixed>  $context  Additional context
     * @param  \Throwable|null  $previous  Previous exception
     */
    public function __construct(
        string $message = '',
        string $code = 'DOMAIN_ERROR',
        array $context = [],
        ?\Throwable $previous = null,
    ) {
        $this->context = $context;

        parent::__construct($message, 0, $previous);
    }

    /**
     * Get the machine-readable error code.
     */
    public function getErrorCode(): string
    {
        return $this->getCode() === 0 ? 'DOMAIN_ERROR' : (string) $this->getCode();
    }

    /**
     * Get the error context.
     *
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Create an exception from a previous exception.
     */
    public static function from(\Throwable $previous): static
    {
        return new static(
            message: $previous->getMessage(),
            previous: $previous,
        );
    }
}
