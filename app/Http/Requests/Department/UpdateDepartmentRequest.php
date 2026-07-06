<?php

declare(strict_types=1);

namespace App\Http\Requests\Department;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $departmentId = $this->route('department')->id;
        $branchId = $this->route('department')->branch_id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash',
                Rule::unique('departments', 'slug')->where('branch_id', $branchId)->ignore($departmentId),
            ],
            'code' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Department name is required.',
            'slug.required' => 'Department slug is required.',
            'slug.unique' => 'This slug is already taken for this branch.',
            'slug.alpha_dash' => 'Slug may only contain letters, numbers, dashes and underscores.',
            'status.in' => 'Status must be either active or inactive.',
        ];
    }
}
