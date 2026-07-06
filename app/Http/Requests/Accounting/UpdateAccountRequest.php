<?php

declare(strict_types=1);

namespace App\Http\Requests\Accounting;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAccountRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $accountId = $this->route('account')->id;

        return [
            'parent_id' => ['nullable', 'exists:chart_of_accounts,id'],
            'code' => ['required', 'string', 'max:255',
                Rule::unique('chart_of_accounts', 'code')
                    ->where('company_id', $this->route('account')->company_id)
                    ->ignore($accountId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:asset,liability,equity,revenue,expense'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Account code is required.',
            'code.unique' => 'This code is already taken.',
            'name.required' => 'Account name is required.',
            'type.required' => 'Account type is required.',
            'type.in' => 'Account type must be asset, liability, equity, revenue, or expense.',
        ];
    }
}
