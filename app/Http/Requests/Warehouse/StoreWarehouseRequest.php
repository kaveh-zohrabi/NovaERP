<?php

declare(strict_types=1);

namespace App\Http\Requests\Warehouse;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWarehouseRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_id' => ['required', 'exists:companies,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255',
                Rule::unique('warehouses', 'code')->where('company_id', $this->company_id),
            ],
            'address' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'company_id.required' => 'Company is required.',
            'name.required' => 'Warehouse name is required.',
            'code.required' => 'Warehouse code is required.',
            'code.unique' => 'This code is already taken.',
            'status.in' => 'Status must be active or inactive.',
        ];
    }
}
