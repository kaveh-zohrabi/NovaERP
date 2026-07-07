<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Opportunity Details') }}</h2>
            <div class="flex gap-2">
                @if ($opportunity->isOpen())
                    <form method="POST" action="{{ route('opportunities.won', $opportunity) }}">
                        @csrf @method('PATCH')
                        <button type="submit" onclick="return confirm('Mark as won?')" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 transition ease-in-out duration-150">{{ __('Mark Won') }}</button>
                    </form>
                    <form method="POST" action="{{ route('opportunities.lost', $opportunity) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="reason" value="Lost">
                        <button type="submit" onclick="return confirm('Mark as lost?')" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 transition ease-in-out duration-150">{{ __('Mark Lost') }}</button>
                    </form>
                @endif
                <a href="{{ route('opportunities.edit', $opportunity) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">{{ __('Edit') }}</a>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ $opportunity->title }}</h3>
                        <p class="mt-1 text-sm text-gray-600">{{ $opportunity->description ?? '-' }}</p>
                    </div>
                    <div class="text-right">
                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full
                            @if ($opportunity->status === 'won') bg-green-100 text-green-800
                            @elseif ($opportunity->status === 'lost') bg-red-100 text-red-800
                            @else bg-blue-100 text-blue-800 @endif">
                            {{ ucfirst($opportunity->status) }}
                        </span>
                    </div>
                </div>
                <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                    <div><span class="font-medium text-gray-500">{{ __('Customer') }}:</span> {{ $opportunity->customer?->name ?? '-' }}</div>
                    <div><span class="font-medium text-gray-500">{{ __('Pipeline') }}:</span> {{ $opportunity->pipeline?->name ?? '-' }}</div>
                    <div><span class="font-medium text-gray-500">{{ __('Stage') }}:</span> {{ $opportunity->pipelineStage?->name ?? '-' }}</div>
                    <div><span class="font-medium text-gray-500">{{ __('Assigned') }}:</span> {{ $opportunity->assignedEmployee?->user?->name ?? '-' }}</div>
                    <div><span class="font-medium text-gray-500">{{ __('Expected Value') }}:</span> ${{ number_format($opportunity->expected_value, 2) }}</div>
                    <div><span class="font-medium text-gray-500">{{ __('Probability') }}:</span> {{ $opportunity->probability }}%</div>
                    <div><span class="font-medium text-gray-500">{{ __('Expected Close') }}:</span> {{ $opportunity->expected_closing_date?->format('M d, Y') ?? '-' }}</div>
                    @if ($opportunity->lost_reason)
                        <div><span class="font-medium text-red-500">{{ __('Lost Reason') }}:</span> {{ $opportunity->lost_reason }}</div>
                    @endif
                </div>
            </div>

            @if ($opportunity->tasks->count())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="font-medium text-gray-900 mb-4">{{ __('Tasks') }}</h4>
                        @foreach ($opportunity->tasks as $task)
                            <div class="flex items-center gap-3 border-b py-2 last:border-0">
                                <span class="w-2 h-2 rounded-full {{ $task->is_completed ? 'bg-green-400' : 'bg-yellow-400' }}"></span>
                                <span class="text-sm {{ $task->is_completed ? 'line-through text-gray-400' : 'text-gray-900' }}">{{ $task->title }}</span>
                                <span class="text-xs text-gray-400">{{ $task->priority }}</span>
                                @if ($task->due_date)
                                    <span class="text-xs text-gray-400">{{ $task->due_date->format('M d') }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
