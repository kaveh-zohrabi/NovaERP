<?php

declare(strict_types=1);

namespace App\Http\Requests\Company;

/**
 * Shared validation messages for company requests.
 */
final class CompanyRequestMessages
{
    /**
     * @return array<string, string>
     */
    public static function all(): array
    {
        return [
            'name.required' => 'Company name is required.',
            'slug.required' => 'Company slug is required.',
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
