@props(['supplier' => null])

@if ($errors->any())
    <div class="rounded-lg bg-red-50 p-4" role="alert">
        <div class="flex">
            <svg class="h-5 w-5 text-red-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
            <div class="ml-3"><h3 class="text-sm font-medium text-red-800">{{ __('Please fix the following errors:') }}</h3>
                <ul class="mt-2 list-disc list-inside text-sm text-red-700">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        </div>
    </div>
@endif

<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">{{ __('Supplier Information') }}</h3>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <x-input-label for="company_id" :value="__('Company')" />
                <select id="company_id" name="company_id" class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    <option value="">{{ __('Select Company') }}</option>
                    @foreach (\App\Models\Company::orderBy('name')->get() as $company)
                        <option value="{{ $company->id }}" {{ old('company_id', $supplier->company_id ?? '') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('company_id')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="name" :value="__('Supplier Name')" />
                <x-text-input id="name" name="name" :value="old('name', $supplier->name ?? '')" required placeholder="Acme Supplies" class="mt-1.5" />
                <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" type="email" name="email" :value="old('email', $supplier->email ?? '')" placeholder="contact@supplier.com (optional)" class="mt-1.5" />
                <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="phone" :value="__('Phone')" />
                <x-text-input id="phone" name="phone" :value="old('phone', $supplier->phone ?? '')" placeholder="+1 234 567 8900 (optional)" class="mt-1.5" />
                <x-input-error :messages="$errors->get('phone')" class="mt-1.5" />
            </div>
            <div class="sm:col-span-2">
                <x-input-label for="address" :value="__('Address')" />
                <textarea id="address" name="address" rows="2" class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="123 Supplier St (optional)">{{ old('address', $supplier->address ?? '') }}</textarea>
                <x-input-error :messages="$errors->get('address')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="status" :value="__('Status')" />
                <select id="status" name="status" class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    <option value="active" {{ old('status', $supplier->status ?? 'active') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="inactive" {{ old('status', $supplier->status ?? '') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                </select>
                <x-input-error :messages="$errors->get('status')" class="mt-1.5" />
            </div>
        </div>
    </div>
</div>
