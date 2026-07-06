<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Company') }}
            </h2>
            <a
                href="{{ route('companies.show', $company) }}"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
                {{ __('Back to Company') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('companies.update', $company) }}" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PUT')

                @include('companies._form', ['company' => $company])

                {{-- Submit --}}
                <div class="flex items-center justify-end gap-3">
                    <a
                        href="{{ route('companies.show', $company) }}"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        {{ __('Cancel') }}
                    </a>
                    <x-primary-button>
                        {{ __('Update Company') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
