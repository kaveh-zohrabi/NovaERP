<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ ucfirst(str_replace('_', ' ', $type)) }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('reports.export', $type) }}?format=csv&{{ http_build_query(request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 transition ease-in-out duration-150">{{ __('Export CSV') }}</a>
                <a href="{{ route('reports.export', $type) }}?format=pdf&{{ http_build_query(request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 transition ease-in-out duration-150">{{ __('Export PDF') }}</a>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <form method="GET" action="{{ route('reports.show', $type) }}">
                    <div class="flex gap-2 items-end">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Start Date') }}</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('End Date') }}</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">{{ __('Filter') }}</button>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if (empty($data))
                        <p class="text-sm text-gray-500">{{ __('No data available for this report.') }}</p>
                    @else
                        @php
                            $rows = $data['accounts'] ?? $data['products'] ?? $data['items'] ?? $data['customers'] ?? $data['movements'] ?? $data['daily_sales'] ?? [];
                            $summaryKeys = collect($data)->filter(fn ($v) => ! is_array($v) && ! is_null($v) && ! is_object($v) && $v !== ($data['period'] ?? null));
                        @endphp

                        @if ($summaryKeys->isNotEmpty())
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                                @foreach ($summaryKeys as $key => $value)
                                    <div class="border rounded-lg p-3">
                                        <p class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $key)) }}</p>
                                        <p class="text-lg font-semibold text-gray-900">{{ is_numeric($value) ? number_format((float) $value, 2) : $value }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if (isset($data['revenue']))
                            <div class="mb-4">
                                <h4 class="font-medium text-gray-900">Revenue: ${{ number_format($data['revenue']['total'], 2) }}</h4>
                            </div>
                        @endif

                        @if (isset($data['expenses']))
                            <div class="mb-4">
                                <h4 class="font-medium text-gray-900">Expenses: ${{ number_format($data['expenses']['total'], 2) }}</h4>
                            </div>
                        @endif

                        @if (isset($data['net_income']))
                            <div class="mb-6">
                                <h4 class="font-semibold {{ $data['net_income'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    Net Income: ${{ number_format($data['net_income'], 2) }}
                                </h4>
                            </div>
                        @endif

                        @if (! empty($rows) && is_array($rows))
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            @foreach (array_keys((array) $rows[0]) as $header)
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ ucfirst(str_replace('_', ' ', $header)) }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($rows as $row)
                                            <tr class="hover:bg-gray-50">
                                                @foreach ($row as $cell)
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ is_numeric($cell) ? number_format((float) $cell, 2) : ($cell ?? '-') }}</td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">{{ __('No data found.') }}</p>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
