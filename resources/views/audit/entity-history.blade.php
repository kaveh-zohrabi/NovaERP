<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Entity History') }}: {{ class_basename(get_class($model)) }} #{{ $model->id }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Event') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('User') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Changes') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($history as $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ $log->event }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->user?->name ?? 'System' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        @if ($log->old_values && $log->new_values)
                                            @php
                                                $changes = collect($log->new_values)->filter(fn ($v, $k) => isset($log->old_values[$k]) && $log->old_values[$k] !== $v);
                                            @endphp
                                            @foreach ($changes as $key => $value)
                                                <span class="text-gray-400">{{ $key }}:</span>
                                                <span class="text-red-500 line-through">{{ is_array($log->old_values[$key]) ? 'array' : $log->old_values[$key] }}</span>
                                                &rarr;
                                                <span class="text-green-600">{{ is_array($value) ? 'array' : $value }}</span><br>
                                            @endforeach
                                        @elseif ($log->event === 'created')
                                            <span class="text-green-600">{{ __('Record created') }}</span>
                                        @elseif ($log->event === 'deleted')
                                            <span class="text-red-600">{{ __('Record deleted') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-12 text-center"><h3 class="text-sm font-medium text-gray-900">{{ __('No history found') }}</h3></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
