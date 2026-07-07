<?php

declare(strict_types=1);

namespace App\Http\Requests\CRM;

use Illuminate\Foundation\Http\FormRequest;

class StoreOpportunityRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'company_id' => ['required', 'exists:companies,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'lead_id' => ['nullable', 'exists:leads,id'],
            'pipeline_id' => ['required', 'exists:pipelines,id'],
            'pipeline_stage_id' => ['required', 'exists:pipeline_stages,id'],
            'assigned_to' => ['nullable', 'exists:employees,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'expected_value' => ['required', 'numeric', 'min:0'],
            'probability' => ['required', 'numeric', 'min:0', 'max:100'],
            'expected_closing_date' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_id.required' => 'Company is required.',
            'pipeline_id.required' => 'Pipeline is required.',
            'pipeline_stage_id.required' => 'Pipeline stage is required.',
            'title.required' => 'Title is required.',
            'expected_value.required' => 'Expected value is required.',
            'expected_value.min' => 'Expected value must be zero or more.',
            'probability.min' => 'Probability must be between 0 and 100.',
            'probability.max' => 'Probability must be between 0 and 100.',
        ];
    }
}
