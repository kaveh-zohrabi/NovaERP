@props(['company' => null])

@php
    $isEdit = $company !== null;
@endphp

{{-- Validation Errors --}}
@if ($errors->any())
    <div class="rounded-lg bg-red-50 p-4" role="alert" aria-live="polite">
        <div class="flex">
            <svg class="h-5 w-5 text-red-400 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
            </svg>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">
                    {{ __('Please fix the following errors:') }}
                </h3>
                <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

{{-- Identity Section --}}
<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
            {{ __('Identity') }}
        </h3>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <x-input-label for="name" :value="__('Company Name')" />
                <div class="mt-1.5">
                    <x-text-input
                        id="name"
                        name="name"
                        :value="old('name', $company->name ?? '')"
                        required
                        autofocus
                        placeholder="Acme Corp"
                        :aria-invalid="$errors->has('name')"
                        :aria-describedby="$errors->has('name') ? 'name-error' : null"
                    />
                </div>
                <x-input-error id="name-error" :messages="$errors->get('name')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="slug" :value="__('Slug')" />
                <div class="mt-1.5">
                    <x-text-input
                        id="slug"
                        name="slug"
                        :value="old('slug', $company->slug ?? '')"
                        required
                        placeholder="acme-corp"
                        :aria-invalid="$errors->has('slug')"
                        :aria-describedby="$errors->has('slug') ? 'slug-error' : null"
                    />
                </div>
                <x-input-error id="slug-error" :messages="$errors->get('slug')" class="mt-1.5" />
            </div>

            <div class="sm:col-span-2">
                <x-input-label for="legal_name" :value="__('Legal Name')" />
                <div class="mt-1.5">
                    <x-text-input
                        id="legal_name"
                        name="legal_name"
                        :value="old('legal_name', $company->legal_name ?? '')"
                        placeholder="Acme Corporation LLC (optional)"
                    />
                </div>
                <x-input-error :messages="$errors->get('legal_name')" class="mt-1.5" />
            </div>
        </div>
    </div>
</div>

{{-- Legal & Tax Section --}}
<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
            {{ __('Legal & Tax') }}
        </h3>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <x-input-label for="registration_number" :value="__('Registration Number')" />
                <div class="mt-1.5">
                    <x-text-input
                        id="registration_number"
                        name="registration_number"
                        :value="old('registration_number', $company->registration_number ?? '')"
                        placeholder="123456789 (optional)"
                    />
                </div>
                <x-input-error :messages="$errors->get('registration_number')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="tax_number" :value="__('Tax Number')" />
                <div class="mt-1.5">
                    <x-text-input
                        id="tax_number"
                        name="tax_number"
                        :value="old('tax_number', $company->tax_number ?? '')"
                        placeholder="US-123456789 (optional)"
                    />
                </div>
                <x-input-error :messages="$errors->get('tax_number')" class="mt-1.5" />
            </div>
        </div>
    </div>
</div>

{{-- Contact Section --}}
<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
            {{ __('Contact') }}
        </h3>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <div class="mt-1.5">
                    <x-text-input
                        id="email"
                        type="email"
                        name="email"
                        :value="old('email', $company->email ?? '')"
                        required
                        placeholder="info@acme.com"
                        :aria-invalid="$errors->has('email')"
                        :aria-describedby="$errors->has('email') ? 'email-error' : null"
                    />
                </div>
                <x-input-error id="email-error" :messages="$errors->get('email')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="phone" :value="__('Phone')" />
                <div class="mt-1.5">
                    <x-text-input
                        id="phone"
                        name="phone"
                        :value="old('phone', $company->phone ?? '')"
                        placeholder="+1 234 567 8900 (optional)"
                    />
                </div>
                <x-input-error :messages="$errors->get('phone')" class="mt-1.5" />
            </div>

            <div class="sm:col-span-2">
                <x-input-label for="website" :value="__('Website')" />
                <div class="mt-1.5">
                    <x-text-input
                        id="website"
                        name="website"
                        :value="old('website', $company->website ?? '')"
                        placeholder="https://acme.com (optional)"
                        :aria-invalid="$errors->has('website')"
                        :aria-describedby="$errors->has('website') ? 'website-error' : null"
                    />
                </div>
                <x-input-error id="website-error" :messages="$errors->get('website')" class="mt-1.5" />
            </div>
        </div>
    </div>
</div>

{{-- Address Section --}}
<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
            {{ __('Address') }}
        </h3>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <x-input-label for="address" :value="__('Street Address')" />
                <div class="mt-1.5">
                    <x-textarea-input
                        id="address"
                        name="address"
                        :value="old('address', $company->address ?? '')"
                        rows="2"
                        placeholder="123 Business St, Suite 100 (optional)"
                    />
                </div>
                <x-input-error :messages="$errors->get('address')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="city" :value="__('City')" />
                <div class="mt-1.5">
                    <x-text-input
                        id="city"
                        name="city"
                        :value="old('city', $company->city ?? '')"
                        placeholder="New York (optional)"
                    />
                </div>
                <x-input-error :messages="$errors->get('city')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="state" :value="__('State / Province')" />
                <div class="mt-1.5">
                    <x-text-input
                        id="state"
                        name="state"
                        :value="old('state', $company->state ?? '')"
                        placeholder="NY (optional)"
                    />
                </div>
                <x-input-error :messages="$errors->get('state')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="country" :value="__('Country')" />
                <div class="mt-1.5">
                    <x-text-input
                        id="country"
                        name="country"
                        :value="old('country', $company->country ?? '')"
                        placeholder="US (optional)"
                        maxlength="2"
                    />
                </div>
                <x-input-error :messages="$errors->get('country')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="postal_code" :value="__('Postal Code')" />
                <div class="mt-1.5">
                    <x-text-input
                        id="postal_code"
                        name="postal_code"
                        :value="old('postal_code', $company->postal_code ?? '')"
                        placeholder="10001 (optional)"
                    />
                </div>
                <x-input-error :messages="$errors->get('postal_code')" class="mt-1.5" />
            </div>
        </div>
    </div>
</div>

{{-- Branding & Status --}}
<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
            {{ __('Branding & Status') }}
        </h3>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <x-input-label for="logo" :value="__('Logo')" />
                <div class="mt-1.5">
                    @if ($isEdit && $company->logo)
                        <div class="mb-3">
                            <img src="{{ $company->logo_url }}" alt="{{ $company->name }}" class="h-16 w-16 rounded-lg object-cover">
                        </div>
                    @endif
                    <x-file-input
                        id="logo"
                        name="logo"
                        accept="image/jpeg,image/png,image/svg+xml"
                        :aria-invalid="$errors->has('logo')"
                        :aria-describedby="$errors->has('logo') ? 'logo-error' : null"
                    />
                </div>
                <p class="mt-1 text-xs text-gray-500">{{ __('JPEG, PNG, or SVG. Max 2MB.') }}</p>
                <x-input-error id="logo-error" :messages="$errors->get('logo')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="status" :value="__('Status')" />
                <div class="mt-1.5">
                    <x-select-input
                        id="status"
                        name="status"
                        required
                        :aria-invalid="$errors->has('status')"
                    >
                        <option value="active" {{ old('status', $company->status ?? 'active') === 'active' ? 'selected' : '' }}>
                            {{ __('Active') }}
                        </option>
                        <option value="inactive" {{ old('status', $company->status ?? 'active') === 'inactive' ? 'selected' : '' }}>
                            {{ __('Inactive') }}
                        </option>
                    </x-select-input>
                </div>
                <x-input-error :messages="$errors->get('status')" class="mt-1.5" />
            </div>
        </div>
    </div>
</div>
