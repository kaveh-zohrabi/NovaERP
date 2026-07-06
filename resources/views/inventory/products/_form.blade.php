@props(['product' => null])

@if ($errors->any())
    <div class="rounded-lg bg-red-50 p-4" role="alert">
        <div class="flex">
            <svg class="h-5 w-5 text-red-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">{{ __('Please fix the following errors:') }}</h3>
                <ul class="mt-2 list-disc list-inside text-sm text-red-700">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        </div>
    </div>
@endif

<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">{{ __('Product Information') }}</h3>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <x-input-label for="company_id" :value="__('Company')" />
                <select id="company_id" name="company_id" class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    <option value="">{{ __('Select Company') }}</option>
                    @foreach (\App\Models\Company::orderBy('name')->get() as $company)
                        <option value="{{ $company->id }}" {{ old('company_id', $product->company_id ?? '') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('company_id')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="category_id" :value="__('Category')" />
                <select id="category_id" name="category_id" class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">{{ __('Select Category (optional)') }}</option>
                    @foreach (\App\Models\Category::where('status', 'active')->orderBy('name')->get() as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('category_id')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="name" :value="__('Product Name')" />
                <x-text-input id="name" name="name" :value="old('name', $product->name ?? '')" required placeholder="Widget Pro" class="mt-1.5" />
                <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="sku" :value="__('SKU')" />
                <x-text-input id="sku" name="sku" :value="old('sku', $product->sku ?? '')" required placeholder="WGT-PRO-001" class="mt-1.5" />
                <x-input-error :messages="$errors->get('sku')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="cost_price" :value="__('Cost Price')" />
                <x-text-input id="cost_price" type="number" step="0.01" min="0" name="cost_price" :value="old('cost_price', $product->cost_price ?? '0')" required class="mt-1.5" />
                <x-input-error :messages="$errors->get('cost_price')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="selling_price" :value="__('Selling Price')" />
                <x-text-input id="selling_price" type="number" step="0.01" min="0" name="selling_price" :value="old('selling_price', $product->selling_price ?? '0')" required class="mt-1.5" />
                <x-input-error :messages="$errors->get('selling_price')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="status" :value="__('Status')" />
                <select id="status" name="status" class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    <option value="active" {{ old('status', $product->status ?? 'active') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="inactive" {{ old('status', $product->status ?? '') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                </select>
                <x-input-error :messages="$errors->get('status')" class="mt-1.5" />
            </div>
        </div>
    </div>
</div>
