@props(['position' => null])

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
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">{{ __('Position Information') }}</h3>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <x-input-label for="department_id" :value="__('Department')" />
                <div class="mt-1.5">
                    <select id="department_id" name="department_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        <option value="">{{ __('Select Department') }}</option>
                        @foreach (\App\Models\Department::where('status', 'active')->orderBy('name')->get() as $department)
                            <option value="{{ $department->id }}" {{ old('department_id', $position->department_id ?? '') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <x-input-error :messages="$errors->get('department_id')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="company_id" :value="__('Company')" />
                <div class="mt-1.5">
                    <select id="company_id" name="company_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        <option value="">{{ __('Select Company') }}</option>
                        @foreach (\App\Models\Company::orderBy('name')->get() as $company)
                            <option value="{{ $company->id }}" {{ old('company_id', $position->company_id ?? '') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <x-input-error :messages="$errors->get('company_id')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="name" :value="__('Position Title')" />
                <div class="mt-1.5">
                    <x-text-input id="name" name="name" :value="old('name', $position->name ?? '')" required placeholder="Sales Manager" />
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="slug" :value="__('Slug')" />
                <div class="mt-1.5">
                    <x-text-input id="slug" name="slug" :value="old('slug', $position->slug ?? '')" placeholder="sales-manager (optional)" />
                </div>
                <x-input-error :messages="$errors->get('slug')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="code" :value="__('Code')" />
                <div class="mt-1.5">
                    <x-text-input id="code" name="code" :value="old('code', $position->code ?? '')" placeholder="MGR-SALES-01 (optional)" />
                </div>
                <x-input-error :messages="$errors->get('code')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="status" :value="__('Status')" />
                <div class="mt-1.5">
                    <select id="status" name="status" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        <option value="active" {{ old('status', $position->status ?? 'active') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="inactive" {{ old('status', $position->status ?? 'active') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    </select>
                </div>
                <x-input-error :messages="$errors->get('status')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="min_salary" :value="__('Minimum Salary')" />
                <div class="mt-1.5">
                    <x-text-input id="min_salary" name="min_salary" type="number" step="0.01" min="0" :value="old('min_salary', $position->min_salary ?? '')" placeholder="50000 (optional)" />
                </div>
                <x-input-error :messages="$errors->get('min_salary')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="max_salary" :value="__('Maximum Salary')" />
                <div class="mt-1.5">
                    <x-text-input id="max_salary" name="max_salary" type="number" step="0.01" min="0" :value="old('max_salary', $position->max_salary ?? '')" placeholder="80000 (optional)" />
                </div>
                <x-input-error :messages="$errors->get('max_salary')" class="mt-1.5" />
            </div>

            <div class="sm:col-span-2">
                <x-input-label for="description" :value="__('Description')" />
                <div class="mt-1.5">
                    <textarea id="description" name="description" rows="3" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Optional description (optional)">{{ old('description', $position->description ?? '') }}</textarea>
                </div>
                <x-input-error :messages="$errors->get('description')" class="mt-1.5" />
            </div>
        </div>
    </div>
</div>
