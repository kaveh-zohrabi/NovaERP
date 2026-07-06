<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $branch->name }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('branches.edit', $branch) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('branches.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
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
                            <x-input-label :value="__('Name')" />
                            <p class="mt-1 text-sm text-gray-900">{{ $branch->name }}</p>
                        </div>
                        <div>
                            <x-input-label :value="__('Slug')" />
                            <p class="mt-1 text-sm text-gray-900">{{ $branch->slug }}</p>
                        </div>
                        <div>
                            <x-input-label :value="__('Company')" />
                            <p class="mt-1 text-sm text-gray-900">{{ $branch->company->name ?? '-' }}</p>
                        </div>
                        <div>
                            <x-input-label :value="__('Status')" />
                            <p class="mt-1">
                                @if ($branch->status === 'active')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ __('Active') }}</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ __('Inactive') }}</span>
                                @endif
                            </p>
                        </div>
                        @if ($branch->full_address)
                            <div class="sm:col-span-2">
                                <x-input-label :value="__('Address')" />
                                <p class="mt-1 text-sm text-gray-900">{{ $branch->full_address }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
