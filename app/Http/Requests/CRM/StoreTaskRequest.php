<?php

declare(strict_types=1);

namespace App\Http\Requests\CRM;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'company_id' => ['required', 'exists:companies,id'],
            'assigned_to' => ['nullable', 'exists:employees,id'],
            'taskable_type' => ['nullable', 'string'],
            'taskable_id' => ['nullable', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'priority' => ['required', 'string', 'in:low,medium,high,urgent'],
            'due_date' => ['nullable', 'date'],
            'reminder_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_id.required' => 'Company is required.',
            'title.required' => 'Title is required.',
            'priority.required' => 'Priority is required.',
            'priority.in' => 'Priority must be low, medium, high, or urgent.',
        ];
    }
}
