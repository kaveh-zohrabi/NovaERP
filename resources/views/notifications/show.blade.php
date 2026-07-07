<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $notification->title }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('notifications.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">{{ __('Back') }}</a>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center gap-3 mb-4">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        @if ($notification->priority === 'urgent') bg-red-100 text-red-800
                        @elseif ($notification->priority === 'high') bg-orange-100 text-orange-800
                        @elseif ($notification->priority === 'normal') bg-blue-100 text-blue-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($notification->priority) }}
                    </span>
                    <span class="text-sm text-gray-500">{{ __('Type:') }} {{ $notification->type }}</span>
                    <span class="text-sm text-gray-500">{{ $notification->created_at->format('M d, Y H:i') }}</span>
                </div>

                <div class="prose max-w-none">
                    <p class="text-gray-700">{{ $notification->message }}</p>
                </div>

                @if ($notification->data)
                    <div class="mt-6 border-t pt-4">
                        <h4 class="font-medium text-gray-900 mb-2">{{ __('Additional Data') }}</h4>
                        <pre class="bg-gray-50 rounded-lg p-4 text-sm overflow-auto">{{ json_encode($notification->data, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                @endif

                <div class="mt-6 flex gap-2">
                    @if ($notification->isUnread())
                        <form method="POST" action="{{ route('notifications.mark-read', $notification) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 transition ease-in-out duration-150">{{ __('Mark as Read') }}</button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('notifications.archive', $notification) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 transition ease-in-out duration-150">{{ __('Archive') }}</button>
                    </form>
                    <form method="POST" action="{{ route('notifications.destroy', $notification) }}">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this notification?')" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 transition ease-in-out duration-150">{{ __('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
