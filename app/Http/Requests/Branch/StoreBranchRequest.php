<?php

declare(strict_types=1);

namespace App\Http\Requests\Branch;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBranchRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_id' => ['required', 'exists:companies,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash',
                Rule::unique('branches', 'slug')->where('company_id', $this->route('company')?->id ?? $this->company_id),
            ],
            'code' => ['nullable', 'string', 'max:255',
                Rule::unique('branches', 'code')->where('company_id', $this->company_id)->ignore($this->route('branch')?->id),
            ],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:2'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'is_headquarters' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'company_id.required' => 'Company is required.',
            'company_id.exists' => 'Selected company does not exist.',
            'name.required' => 'Branch name is required.',
            'slug.required' => 'Branch slug is required.',
            'slug.unique' => 'This slug is already taken for this company.',
            'slug.alpha_dash' => 'Slug may only contain letters, numbers, dashes and underscores.',
            'code.unique' => 'This code is already taken for this company.',
            'email.email' => 'Please provide a valid email address.',
            'status.in' => 'Status must be either active or inactive.',
        ];
    }
}
