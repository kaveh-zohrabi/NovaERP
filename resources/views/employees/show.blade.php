<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $employee->full_name }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('employees.edit', $employee) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('employees.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <x-input-label :value="__('Full Name')" />
                            <p class="mt-1 text-sm text-gray-900">{{ $employee->full_name }}</p>
                        </div>
                        <div>
                            <x-input-label :value="__('Email')" />
                            <p class="mt-1 text-sm text-gray-900">{{ $employee->email }}</p>
                        </div>
                        <div>
                            <x-input-label :value="__('Company')" />
                            <p class="mt-1 text-sm text-gray-900">{{ $employee->company->name ?? '-' }}</p>
                        </div>
                        <div>
                            <x-input-label :value="__('Branch')" />
                            <p class="mt-1 text-sm text-gray-900">{{ $employee->branch->name ?? '-' }}</p>
                        </div>
                        <div>
                            <x-input-label :value="__('Department')" />
                            <p class="mt-1 text-sm text-gray-900">{{ $employee->department->name ?? '-' }}</p>
                        </div>
                        <div>
                            <x-input-label :value="__('Position')" />
                            <p class="mt-1 text-sm text-gray-900">{{ $employee->position->name ?? '-' }}</p>
                        </div>
                        <div>
                            <x-input-label :value="__('Status')" />
                            <p class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $employee->status->cssClass() }}">
                                    {{ $employee->status->label() }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <x-input-label :value="__('Employment Type')" />
                            <p class="mt-1 text-sm text-gray-900">{{ $employee->employment_type->label() }}</p>
                        </div>
                        <div>
                            <x-input-label :value="__('Hire Date')" />
                            <p class="mt-1 text-sm text-gray-900">{{ $employee->hire_date->format('M d, Y') }}</p>
                        </div>
                        @if ($employee->salary)
                            <div>
                                <x-input-label :value="__('Salary')" />
                                <p class="mt-1 text-sm text-gray-900">${{ number_format($employee->salary, 2) }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
