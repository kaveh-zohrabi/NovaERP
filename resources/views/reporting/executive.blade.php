<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Executive Dashboard') }}</h2>
            <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">{{ __('View Reports') }}</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('executive') }}" class="mb-6">
                <div class="flex gap-2 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Start Date') }}</label>
                        <input type="date" name="start_date" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" class="mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('End Date') }}</label>
                        <input type="date" name="end_date" value="{{ request('end_date', now()->endOfMonth()->format('Y-m-d')) }}" class="mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">{{ __('Apply') }}</button>
                </div>
            </form>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">{{ __('Total Revenue') }}</p>
                    <p class="mt-1 text-2xl font-semibold text-green-600">${{ number_format($metrics['revenue'], 2) }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">{{ __('Total Expenses') }}</p>
                    <p class="mt-1 text-2xl font-semibold text-red-600">${{ number_format($metrics['expenses'], 2) }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">{{ __('Net Profit') }}</p>
                    <p class="mt-1 text-2xl font-semibold {{ $metrics['net_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">${{ number_format($metrics['net_profit'], 2) }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">{{ __('Lead Conversion') }}</p>
                    <p class="mt-1 text-2xl font-semibold text-indigo-600">{{ $metrics['lead_conversion_rate'] }}%</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">{{ __('Total Customers') }}</p>
                    <p class="mt-1 text-xl font-semibold text-gray-900">{{ number_format($metrics['total_customers']) }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">{{ __('Total Leads') }}</p>
                    <p class="mt-1 text-xl font-semibold text-gray-900">{{ number_format($metrics['total_leads']) }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">{{ __('Low Stock Items') }}</p>
                    <p class="mt-1 text-xl font-semibold {{ $metrics['low_stock_count'] > 0 ? 'text-red-600' : 'text-green-600' }}">{{ $metrics['low_stock_count'] }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">{{ __('Pipeline Value') }}</p>
                    <p class="mt-1 text-xl font-semibold text-gray-900">${{ number_format($metrics['opportunity_value'], 2) }}</p>
                    <p class="text-xs text-gray-400">{{ $metrics['open_opportunities'] }} open opportunities</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-medium text-gray-900 mb-4">{{ __('Quick Reports') }}</h3>
                    <div class="space-y-2">
                        <a href="{{ route('reports.show', 'sales_overview') }}" class="block text-indigo-600 hover:text-indigo-500 text-sm">{{ __('Sales Overview') }}</a>
                        <a href="{{ route('reports.show', 'inventory_valuation') }}" class="block text-indigo-600 hover:text-indigo-500 text-sm">{{ __('Inventory Valuation') }}</a>
                        <a href="{{ route('reports.show', 'profit_loss') }}" class="block text-indigo-600 hover:text-indigo-500 text-sm">{{ __('Profit & Loss') }}</a>
                        <a href="{{ route('reports.show', 'trial_balance') }}" class="block text-indigo-600 hover:text-indigo-500 text-sm">{{ __('Trial Balance') }}</a>
                        <a href="{{ route('reports.show', 'low_stock') }}" class="block text-indigo-600 hover:text-indigo-500 text-sm">{{ __('Low Stock Report') }}</a>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-medium text-gray-900 mb-4">{{ __('Dashboards') }}</h3>
                    <a href="{{ route('dashboards.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 transition ease-in-out duration-150">{{ __('Manage Dashboards') }}</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
