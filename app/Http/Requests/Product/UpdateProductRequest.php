<?php

declare(strict_types=1);

namespace App\Http\Requests\Product;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('product')->id;
        $companyId = $this->route('product')->company_id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash',
                Rule::unique('products', 'slug')->where('company_id', $companyId)->ignore($productId),
            ],
            'sku' => ['required', 'string', 'max:255',
                Rule::unique('products', 'sku')->where('company_id', $companyId)->ignore($productId),
            ],
            'barcode' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0', 'gte:cost_price'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required.',
            'slug.required' => 'Product slug is required.',
            'slug.unique' => 'This slug is already taken.',
            'sku.required' => 'SKU is required.',
            'sku.unique' => 'This SKU is already taken.',
            'cost_price.required' => 'Cost price is required.',
            'selling_price.required' => 'Selling price is required.',
            'selling_price.gte' => 'Selling price must be greater than or equal to cost price.',
            'status.in' => 'Status must be active or inactive.',
        ];
    }
}
