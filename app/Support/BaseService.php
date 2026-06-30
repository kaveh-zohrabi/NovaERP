<?php

declare(strict_types=1);

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\DB;

/**
 * Abstract base class for business logic services.
 *
 * Provides common functionality for database transactions,
 * event dispatching, and service lifecycle management.
 *
 * @example
 * class InventoryService extends BaseService
 * {
 *     public function transferStock(Product $product, int $quantity): StockTransfer
 *     {
 *         return $this->transaction(function () use ($product, $quantity) {
 *             // Business logic here
 *         });
 *     }
 * }
 */
abstract class BaseService
{
    /**
     * Execute a callback within a database transaction.
     *
     * If the callback returns a value, it will be returned from this method.
     * If an exception is thrown, the transaction is rolled back and the exception is re-thrown.
     *
     * @template T
     *
     * @param  Closure(): T  $callback
     * @return T
     */
    protected function transaction(Closure $callback): mixed
    {
        return DB::transaction($callback);
    }

    /**
     * Execute a callback without firing model events.
     *
     * Useful for bulk operations where events are not needed.
     *
     * @template T
     *
     * @param  Closure(): T  $callback
     * @return T
     */
    protected function withoutEvents(Closure $callback): mixed
    {
        return DB::withoutEvents($callback);
    }

    /**
     * Execute a callback on a specific database connection.
     *
     * @template T
     *
     * @param  Closure(): T  $callback
     * @return T
     */
    protected function onConnection(string $connection, Closure $callback): mixed
    {
        return DB::connection($connection)->transaction($callback);
    }
}
