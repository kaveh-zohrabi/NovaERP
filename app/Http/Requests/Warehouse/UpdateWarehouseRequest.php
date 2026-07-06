<?php

declare(strict_types=1);

namespace App\Http\Requests\Warehouse;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWarehouseRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $warehouseId = $this->route('warehouse')->id;
        $companyId = $this->route('warehouse')->company_id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255',
                Rule::unique('warehouses', 'code')->where('company_id', $companyId)->ignore($warehouseId),
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
            'name.required' => 'Warehouse name is required.',
            'code.required' => 'Warehouse code is required.',
            'code.unique' => 'This code is already taken.',
            'status.in' => 'Status must be active or inactive.',
        ];
    }
}
