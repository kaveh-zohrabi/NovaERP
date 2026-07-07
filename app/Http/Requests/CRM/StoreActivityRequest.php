<?php

declare(strict_types=1);

namespace App\Http\Requests\CRM;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivityRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'company_id' => ['required', 'exists:companies,id'],
            'subjectable_type' => ['required', 'string'],
            'subjectable_id' => ['required', 'integer'],
            'assigned_to' => ['nullable', 'exists:employees,id'],
            'type' => ['required', 'string', 'in:call,meeting,email,follow_up,demo'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'due_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_id.required' => 'Company is required.',
            'type.required' => 'Activity type is required.',
            'type.in' => 'Activity type must be call, meeting, email, follow_up, or demo.',
            'title.required' => 'Title is required.',
        ];
    }
}
