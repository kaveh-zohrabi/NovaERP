@props(['employee' => null])

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
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">{{ __('Personal Information') }}</h3>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <x-input-label for="first_name" :value="__('First Name')" />
                <div class="mt-1.5">
                    <x-text-input id="first_name" name="first_name" :value="old('first_name', $employee->first_name ?? '')" required placeholder="John" />
                </div>
                <x-input-error :messages="$errors->get('first_name')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="last_name" :value="__('Last Name')" />
                <div class="mt-1.5">
                    <x-text-input id="last_name" name="last_name" :value="old('last_name', $employee->last_name ?? '')" required placeholder="Doe" />
                </div>
                <x-input-error :messages="$errors->get('last_name')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <div class="mt-1.5">
                    <x-text-input id="email" type="email" name="email" :value="old('email', $employee->email ?? '')" required placeholder="john@example.com" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="phone" :value="__('Phone')" />
                <div class="mt-1.5">
                    <x-text-input id="phone" name="phone" :value="old('phone', $employee->phone ?? '')" placeholder="+1 234 567 8900 (optional)" />
                </div>
                <x-input-error :messages="$errors->get('phone')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="employee_code" :value="__('Employee Code')" />
                <div class="mt-1.5">
                    <x-text-input id="employee_code" name="employee_code" :value="old('employee_code', $employee->employee_code ?? '')" placeholder="EMP-001 (optional)" />
                </div>
                <x-input-error :messages="$errors->get('employee_code')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
                <div class="mt-1.5">
                    <x-text-input id="date_of_birth" type="date" name="date_of_birth" :value="old('date_of_birth', $employee->date_of_birth?->format('Y-m-d') ?? '')" />
                </div>
                <x-input-error :messages="$errors->get('date_of_birth')" class="mt-1.5" />
            </div>
        </div>
    </div>
</div>

<div class="bg-white shadow sm:rounded-lg mt-6">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">{{ __('Employment Details') }}</h3>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <x-input-label for="company_id" :value="__('Company')" />
                <div class="mt-1.5">
                    <select id="company_id" name="company_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        <option value="">{{ __('Select Company') }}</option>
                        @foreach (\App\Models\Company::orderBy('name')->get() as $company)
                            <option value="{{ $company->id }}" {{ old('company_id', $employee->company_id ?? '') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
                <x-input-error :messages="$errors->get('company_id')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="branch_id" :value="__('Branch')" />
                <div class="mt-1.5">
                    <select id="branch_id" name="branch_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">{{ __('Select Branch (optional)') }}</option>
                        @foreach (\App\Models\Branch::where('status', 'active')->orderBy('name')->get() as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', $employee->branch_id ?? '') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <x-input-error :messages="$errors->get('branch_id')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="department_id" :value="__('Department')" />
                <div class="mt-1.5">
                    <select id="department_id" name="department_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">{{ __('Select Department (optional)') }}</option>
                        @foreach (\App\Models\Department::where('status', 'active')->orderBy('name')->get() as $department)
                            <option value="{{ $department->id }}" {{ old('department_id', $employee->department_id ?? '') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <x-input-error :messages="$errors->get('department_id')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="position_id" :value="__('Position')" />
                <div class="mt-1.5">
                    <select id="position_id" name="position_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">{{ __('Select Position (optional)') }}</option>
                        @foreach (\App\Models\Position::where('status', 'active')->orderBy('name')->get() as $position)
                            <option value="{{ $position->id }}" {{ old('position_id', $employee->position_id ?? '') == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                        @endforeach
                    </select>
                </div>
                <x-input-error :messages="$errors->get('position_id')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="hire_date" :value="__('Hire Date')" />
                <div class="mt-1.5">
                    <x-text-input id="hire_date" type="date" name="hire_date" :value="old('hire_date', $employee->hire_date?->format('Y-m-d') ?? date('Y-m-d'))" required />
                </div>
                <x-input-error :messages="$errors->get('hire_date')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="employment_type" :value="__('Employment Type')" />
                <div class="mt-1.5">
                    <select id="employment_type" name="employment_type" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        <option value="full_time" {{ old('employment_type', $employee->employment_type ?? 'full_time') === 'full_time' ? 'selected' : '' }}>{{ __('Full Time') }}</option>
                        <option value="part_time" {{ old('employment_type', $employee->employment_type ?? '') === 'part_time' ? 'selected' : '' }}>{{ __('Part Time') }}</option>
                        <option value="contract" {{ old('employment_type', $employee->employment_type ?? '') === 'contract' ? 'selected' : '' }}>{{ __('Contract') }}</option>
                        <option value="intern" {{ old('employment_type', $employee->employment_type ?? '') === 'intern' ? 'selected' : '' }}>{{ __('Intern') }}</option>
                    </select>
                </div>
                <x-input-error :messages="$errors->get('employment_type')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="status" :value="__('Status')" />
                <div class="mt-1.5">
                    <select id="status" name="status" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        <option value="active" {{ old('status', $employee->status ?? 'active') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="inactive" {{ old('status', $employee->status ?? '') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                        <option value="suspended" {{ old('status', $employee->status ?? '') === 'suspended' ? 'selected' : '' }}>{{ __('Suspended') }}</option>
                        <option value="terminated" {{ old('status', $employee->status ?? '') === 'terminated' ? 'selected' : '' }}>{{ __('Terminated') }}</option>
                    </select>
                </div>
                <x-input-error :messages="$errors->get('status')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="salary" :value="__('Salary')" />
                <div class="mt-1.5">
                    <x-text-input id="salary" type="number" step="0.01" min="0" name="salary" :value="old('salary', $employee->salary ?? '')" placeholder="50000 (optional)" />
                </div>
                <x-input-error :messages="$errors->get('salary')" class="mt-1.5" />
            </div>
        </div>
    </div>
</div>
