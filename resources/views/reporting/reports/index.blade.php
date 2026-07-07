<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Reports') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @php $grouped = collect($reports)->groupBy('category'); @endphp
            @foreach ($grouped as $category => $items)
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $category }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach ($items as $report)
                            <a href="{{ route('reports.show', $report['type']) }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition-shadow">
                                <h4 class="font-medium text-gray-900">{{ $report['name'] }}</h4>
                                <p class="mt-2 text-sm text-gray-500">{{ __('View Report') }} &rarr;</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
