<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Audit Detail') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div><dt class="font-medium text-gray-500">{{ __('Event') }}</dt><dd class="mt-1">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if ($log->event === 'created') bg-green-100 text-green-800
                            @elseif ($log->event === 'deleted') bg-red-100 text-red-800
                            @else bg-blue-100 text-blue-800 @endif">{{ $log->event }}</span>
                    </dd></div>
                    <div><dt class="font-medium text-gray-500">{{ __('Entity') }}</dt><dd class="mt-1 text-gray-900">{{ class_basename($log->auditable_type ?? '') }} #{{ $log->auditable_id ?? '' }}</dd></div>
                    <div><dt class="font-medium text-gray-500">{{ __('User') }}</dt><dd class="mt-1 text-gray-900">{{ $log->user?->name ?? 'System' }}</dd></div>
                    <div><dt class="font-medium text-gray-500">{{ __('IP Address') }}</dt><dd class="mt-1 text-gray-900">{{ $log->ip_address ?? '-' }}</dd></div>
                    <div><dt class="font-medium text-gray-500">{{ __('User Agent') }}</dt><dd class="mt-1 text-gray-900 text-xs break-all">{{ $log->user_agent ?? '-' }}</dd></div>
                    <div><dt class="font-medium text-gray-500">{{ __('Date') }}</dt><dd class="mt-1 text-gray-900">{{ $log->created_at->format('M d, Y H:i:s') }}</dd></div>
                </dl>

                @if ($log->old_values)
                    <div class="mt-6 border-t pt-4">
                        <h4 class="font-medium text-gray-900 mb-2">{{ __('Previous Values') }}</h4>
                        <pre class="bg-red-50 rounded-lg p-4 text-sm overflow-auto max-h-64">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                @endif

                @if ($log->new_values)
                    <div class="mt-4">
                        <h4 class="font-medium text-gray-900 mb-2">{{ __('New Values') }}</h4>
                        <pre class="bg-green-50 rounded-lg p-4 text-sm overflow-auto max-h-64">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                @endif

                @if ($log->metadata)
                    <div class="mt-4">
                        <h4 class="font-medium text-gray-900 mb-2">{{ __('Metadata') }}</h4>
                        <pre class="bg-gray-50 rounded-lg p-4 text-sm overflow-auto">{{ json_encode($log->metadata, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                @endif

                <div class="mt-6">
                    <a href="{{ route('audit.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">{{ __('Back to Audit Log') }}</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
