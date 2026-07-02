<?php

declare(strict_types=1);

namespace App\Http\Requests\Company;

use App\Models\Company;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // ──────────────────────────────────────────────
            // Identity (required)
            // ──────────────────────────────────────────────

            // Company display name (e.g., "Acme Corp")
            'name' => ['required', 'string', 'max:255'],

            // URL-friendly identifier (e.g., "acme-corp")
            // Must be unique across all companies
            'slug' => ['required', 'string', 'max:255', 'unique:companies,slug', 'alpha_dash'],

            // Legal name on official documents (e.g., "Acme Corporation LLC")
            'legal_name' => ['nullable', 'string', 'max:255'],

            // ──────────────────────────────────────────────
            // Legal & Tax (optional)
            // ──────────────────────────────────────────────

            // Government registration number
            'registration_number' => ['nullable', 'string', 'max:255'],

            // Tax identification number
            'tax_number' => ['nullable', 'string', 'max:255'],

            // ──────────────────────────────────────────────
            // Contact (email required)
            // ──────────────────────────────────────────────

            // Primary contact email
            'email' => ['required', 'email', 'max:255'],

            // Contact phone
            'phone' => ['nullable', 'string', 'max:255'],

            // Company website
            'website' => ['nullable', 'url', 'max:255'],

            // ──────────────────────────────────────────────
            // Address (all optional)
            // ──────────────────────────────────────────────

            // Street address
            'address' => ['nullable', 'string', 'max:1000'],

            // City
            'city' => ['nullable', 'string', 'max:255'],

            // State / Province
            'state' => ['nullable', 'string', 'max:255'],

            // Country (ISO code)
            'country' => ['nullable', 'string', 'max:2'],

            // ZIP / Postal code
            'postal_code' => ['nullable', 'string', 'max:20'],

            // ──────────────────────────────────────────────
            // Branding & Status
            // ──────────────────────────────────────────────

            // Logo file (image upload)
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,svg', 'max:2048'],

            // Company status
            'status' => ['required', 'string', 'in:active,inactive'],

            // ──────────────────────────────────────────────
            // Settings (optional JSON)
            // ──────────────────────────────────────────────

            // Company-specific settings
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
            'slug.unique' => 'This slug is already taken.',
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
