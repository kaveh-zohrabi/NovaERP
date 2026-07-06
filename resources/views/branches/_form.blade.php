@props(['branch' => null])

@if ($errors->any())
    <div class="rounded-lg bg-red-50 p-4" role="alert" aria-live="polite">
        <div class="flex">
            <svg class="h-5 w-5 text-red-400 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
            </svg>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">{{ __('Please fix the following errors:') }}</h3>
                <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">{{ __('Branch Information') }}</h3>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <x-input-label for="company_id" :value="__('Company')" />
                <div class="mt-1.5">
                    <select id="company_id" name="company_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        <option value="">{{ __('Select Company') }}</option>
                        @foreach (\App\Models\Company::orderBy('name')->get() as $company)
                            <option value="{{ $company->id }}" {{ old('company_id', $branch->company_id ?? '') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <x-input-error :messages="$errors->get('company_id')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="name" :value="__('Branch Name')" />
                <div class="mt-1.5">
                    <x-text-input id="name" name="name" :value="old('name', $branch->name ?? '')" required autofocus placeholder="New York Office" />
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="slug" :value="__('Slug')" />
                <div class="mt-1.5">
                    <x-text-input id="slug" name="slug" :value="old('slug', $branch->slug ?? '')" required placeholder="new-york-office" />
                </div>
                <x-input-error :messages="$errors->get('slug')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="code" :value="__('Code')" />
                <div class="mt-1.5">
                    <x-text-input id="code" name="code" :value="old('code', $branch->code ?? '')" placeholder="NYC-01 (optional)" />
                </div>
                <x-input-error :messages="$errors->get('code')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <div class="mt-1.5">
                    <x-text-input id="email" type="email" name="email" :value="old('email', $branch->email ?? '')" placeholder="info@branch.com (optional)" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="phone" :value="__('Phone')" />
                <div class="mt-1.5">
                    <x-text-input id="phone" name="phone" :value="old('phone', $branch->phone ?? '')" placeholder="+1 234 567 8900 (optional)" />
                </div>
                <x-input-error :messages="$errors->get('phone')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="status" :value="__('Status')" />
                <div class="mt-1.5">
                    <select id="status" name="status" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        <option value="active" {{ old('status', $branch->status ?? 'active') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="inactive" {{ old('status', $branch->status ?? 'active') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    </select>
                </div>
                <x-input-error :messages="$errors->get('status')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="is_headquarters" :value="__('Headquarters')" />
                <div class="mt-1.5 flex items-center gap-2">
                    <input type="hidden" name="is_headquarters" value="0">
                    <input type="checkbox" id="is_headquarters" name="is_headquarters" value="1" {{ old('is_headquarters', $branch->is_headquarters ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_headquarters" class="text-sm text-gray-700">{{ __('This is the headquarters') }}</label>
                </div>
                <x-input-error :messages="$errors->get('is_headquarters')" class="mt-1.5" />
            </div>
        </div>
    </div>
</div>

<div class="bg-white shadow sm:rounded-lg mt-6">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">{{ __('Address') }}</h3>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <x-input-label for="address" :value="__('Street Address')" />
                <div class="mt-1.5">
                    <textarea id="address" name="address" rows="2" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="123 Business St (optional)">{{ old('address', $branch->address ?? '') }}</textarea>
                </div>
                <x-input-error :messages="$errors->get('address')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="city" :value="__('City')" />
                <div class="mt-1.5">
                    <x-text-input id="city" name="city" :value="old('city', $branch->city ?? '')" placeholder="New York (optional)" />
                </div>
                <x-input-error :messages="$errors->get('city')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="state" :value="__('State / Province')" />
                <div class="mt-1.5">
                    <x-text-input id="state" name="state" :value="old('state', $branch->state ?? '')" placeholder="NY (optional)" />
                </div>
                <x-input-error :messages="$errors->get('state')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="country" :value="__('Country')" />
                <div class="mt-1.5">
                    <x-text-input id="country" name="country" :value="old('country', $branch->country ?? '')" placeholder="US (optional)" maxlength="2" />
                </div>
                <x-input-error :messages="$errors->get('country')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="postal_code" :value="__('Postal Code')" />
                <div class="mt-1.5">
                    <x-text-input id="postal_code" name="postal_code" :value="old('postal_code', $branch->postal_code ?? '')" placeholder="10001 (optional)" />
                </div>
                <x-input-error :messages="$errors->get('postal_code')" class="mt-1.5" />
            </div>
        </div>
    </div>
</div>
