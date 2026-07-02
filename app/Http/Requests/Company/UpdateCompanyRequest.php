<?php

declare(strict_types=1);

namespace App\Http\Requests\Company;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $companyId = $this->route('company')->id;

        return [
            // ──────────────────────────────────────────────
            // Identity (required)
            // ──────────────────────────────────────────────

            'name' => ['required', 'string', 'max:255'],

            // Slug must be unique, but ignore current company
            'slug' => ['required', 'string', 'max:255', 'alpha_dash',
                \Illuminate\Validation\Rule::unique('companies', 'slug')->ignore($companyId),
            ],

            'legal_name' => ['nullable', 'string', 'max:255'],

            // ──────────────────────────────────────────────
            // Legal & Tax (optional)
            // ──────────────────────────────────────────────

            'registration_number' => ['nullable', 'string', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:255'],

            // ──────────────────────────────────────────────
            // Contact
            // ──────────────────────────────────────────────

            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],

            // ──────────────────────────────────────────────
            // Address
            // ──────────────────────────────────────────────

            'address' => ['nullable', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:2'],
            'postal_code' => ['nullable', 'string', 'max:20'],

            // ──────────────────────────────────────────────
            // Branding & Status
            // ──────────────────────────────────────────────

            'logo' => ['nullable', 'image', 'mimes:jpeg,png,svg', 'max:2048'],
            'status' => ['required', 'string', 'in:active,inactive'],

            // ──────────────────────────────────────────────
            // Settings
            // ──────────────────────────────────────────────

            'settings' => ['nullable', 'array'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Company name is required.',
            'slug.required' => 'Company slug is required.',
            'slug.unique' => 'This slug is already taken by another company.',
            'slug.alpha_dash' => 'Slug may only contain letters, numbers, dashes and underscores.',
            'email.required' => 'Contact email is required.',
            'email.email' => 'Please provide a valid email address.',
            'website.url' => 'Please provide a valid URL (e.g., https://example.com)',
            'logo.image' => 'Logo must be an image file.',
            'logo.mimes' => 'Logo must be a JPEG, PNG, or SVG file.',
            'logo.max' => 'Logo must be less than 2MB.',
            'status.in' => 'Status must be either active or inactive.',
        ];
    }
}
