<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Lead Details') }}</h2>
            <div class="flex gap-2">
                @if (!$lead->isConverted())
                    <form method="POST" action="{{ route('leads.convert', $lead) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" onclick="return confirm('{{ __('Convert this lead to a customer?') }}')" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('Convert to Customer') }}</button>
                    </form>
                @endif
                <a href="{{ route('leads.edit', $lead) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('Edit') }}</a>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ $lead->fullName() }}</h3>
                            <p class="mt-1 text-sm text-gray-600">{{ $lead->company_name ?? '-' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full
                                @if ($lead->status === 'won') bg-green-100 text-green-800
                                @elseif ($lead->status === 'lost') bg-red-100 text-red-800
                                @elseif ($lead->status === 'new') bg-blue-100 text-blue-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $lead->status)) }}
                            </span>
                            @if ($lead->isConverted())
                                <span class="ml-2 px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-purple-100 text-purple-800">{{ __('Converted') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div><span class="font-medium text-gray-500">{{ __('Email') }}:</span> {{ $lead->email ?? '-' }}</div>
                        <div><span class="font-medium text-gray-500">{{ __('Phone') }}:</span> {{ $lead->phone ?? '-' }}</div>
                        <div><span class="font-medium text-gray-500">{{ __('Source') }}:</span> {{ $lead->source ?? '-' }}</div>
                        <div><span class="font-medium text-gray-500">{{ __('Assigned To') }}:</span> {{ $lead->assignedEmployee?->user?->name ?? '-' }}</div>
                        <div><span class="font-medium text-gray-500">{{ __('Estimated Value') }}:</span> ${{ number_format($lead->estimated_value ?? 0, 2) }}</div>
                        @if ($lead->lost_reason)
                            <div><span class="font-medium text-red-500">{{ __('Lost Reason') }}:</span> {{ $lead->lost_reason }}</div>
                        @endif
                    </div>
                </div>
            </div>

            @if ($lead->opportunities->count())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="font-medium text-gray-900 mb-4">{{ __('Opportunities') }}</h4>
                        @foreach ($lead->opportunities as $opp)
                            <div class="border rounded-lg p-3 mb-2">
                                <a href="{{ route('opportunities.show', $opp) }}" class="text-indigo-600 hover:text-indigo-500 font-medium">{{ $opp->title }}</a>
                                <span class="ml-2 text-sm text-gray-500">${{ number_format($opp->expected_value, 2) }}</span>
                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if ($opp->status === 'won') bg-green-100 text-green-800
                                    @elseif ($opp->status === 'lost') bg-red-100 text-red-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ ucfirst($opp->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($lead->notes->count())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="font-medium text-gray-900 mb-4">{{ __('Notes') }}</h4>
                        @foreach ($lead->notes as $note)
                            <div class="border-l-4 border-indigo-200 pl-4 mb-3">
                                <p class="text-sm text-gray-700">{{ $note->body }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $note->user->name }} &middot; {{ $note->created_at->diffForHumans() }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
