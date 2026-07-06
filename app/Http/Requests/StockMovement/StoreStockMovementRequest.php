<?php

declare(strict_types=1);

namespace App\Http\Requests\StockMovement;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStockMovementRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id', 'required_with:movement_type:IN,OUT,ADJUSTMENT'],
            'from_warehouse_id' => ['required_with:movement_type:TRANSFER', 'exists:warehouses,id'],
            'to_warehouse_id' => ['required_with:movement_type:TRANSFER', 'exists:warehouses,id', 'neq:from_warehouse_id'],
            'movement_type' => ['required', 'string', 'in:IN,OUT,TRANSFER,ADJUSTMENT'],
            'quantity' => ['required', 'numeric', 'min:-999999.99', 'not_in:0'],
            'notes' => ['nullable', 'string', 'max:1000', 'required_if:movement_type,ADJUSTMENT'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Product is required.',
            'product_id.exists' => 'Selected product does not exist.',
            'warehouse_id.required' => 'Warehouse is required.',
            'warehouse_id.exists' => 'Selected warehouse does not exist.',
            'movement_type.required' => 'Movement type is required.',
            'movement_type.in' => 'Movement type must be IN, OUT, TRANSFER, or ADJUSTMENT.',
            'quantity.required' => 'Quantity is required.',
            'quantity.min' => 'Quantity must be at least 0.01.',
            'notes.required_if' => 'Notes are required for adjustments.',
            'to_warehouse_id.neq' => 'Source and destination warehouses must be different.',
        ];
    }
}
