<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Abstract base class for single-purpose action classes.
 *
 * Actions encapsulate a single business operation.
 * Each action should have one public method: execute().
 *
 * @template TInput
 * @template TOutput
 *
 * @example
 * class CreateInvoice extends BaseAction
 * {
 *     public function execute(CreateInvoiceDTO $dto): Invoice
 *     {
 *         // Single responsibility: create an invoice
 *     }
 * }
 */
abstract class BaseAction
{
    /**
     * Execute the action.
     *
     * @param  TInput  $input
     * @return TOutput
     */
    abstract public function execute(mixed $input): mixed;
}
