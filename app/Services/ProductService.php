<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use App\Support\BaseService;

class ProductService extends BaseService
{
    /**
     * Create a new product.
     */
    public function create(array $data, User $creator): Product
    {
        return Product::create(
            array_merge($data, ['created_by' => $creator->id])
        );
    }

    /**
     * Update a product.
     */
    public function update(Product $product, array $data): Product
    {
        $data['updated_by'] = auth()->id();

        $product->update($data);

        return $product->fresh();
    }

    /**
     * Soft delete a product.
     */
    public function delete(Product $product): array
    {
        $product->delete();

        return [
            'success' => true,
            'message' => 'Product deleted successfully.',
        ];
    }

    /**
     * Restore a soft-deleted product.
     */
    public function restore(Product $product): bool
    {
        if (! $product->trashed()) {
            return false;
        }

        $product->restore();

        return true;
    }
}
