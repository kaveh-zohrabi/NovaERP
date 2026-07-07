<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $dashboard->name }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($dashboard->description)
                <p class="text-sm text-gray-600 mb-6">{{ $dashboard->description }}</p>
            @endif

            @if ($dashboard->widgets->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
                    <h3 class="text-sm font-medium text-gray-900">{{ __('No widgets configured') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Add widgets to customize this dashboard.') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($dashboard->widgets as $widget)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h4 class="font-medium text-gray-900 mb-2">{{ ucfirst(str_replace('_', ' ', $widget->widget_type)) }}</h4>
                            <p class="text-xs text-gray-400">{{ __('Size') }}: {{ $widget->size }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
