<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Order') }} {{ $order->order_number }}</h2>
            <div class="flex gap-2">
                @if ($order->isDraft())
                    <a href="{{ route('orders.edit', $order) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('Edit') }}</a>
                @endif
                <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('Back to List') }}</a>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Order Info --}}
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div><x-input-label :value="__('Supplier')" /><p class="mt-1 text-sm text-gray-900">{{ $order->supplier->name ?? '-' }}</p></div>
                        <div><x-input-label :value="__('Warehouse')" /><p class="mt-1 text-sm text-gray-900">{{ $order->warehouse->name ?? '-' }}</p></div>
                        <div><x-input-label :value="__('Order Date')" /><p class="mt-1 text-sm text-gray-900">{{ $order->order_date->format('M d, Y') }}</p></div>
                        <div><x-input-label :value="__('Status')" /><p class="mt-1"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full @if ($order->status === 'draft') bg-gray-100 text-gray-800 @elseif ($order->status === 'approved') bg-blue-100 text-blue-800 @elseif ($order->status === 'completed') bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span></p></div>
                        <div><x-input-label :value="__('Total Amount')" /><p class="mt-1 text-sm text-gray-900 font-semibold">${{ number_format($order->total_amount, 2) }}</p></div>
                        @if ($order->notes)<div class="sm:col-span-2"><x-input-label :value="__('Notes')" /><p class="mt-1 text-sm text-gray-900">{{ $order->notes }}</p></div>@endif
                    </div>
                </div>
            </div>

            {{-- Order Items --}}
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">{{ __('Order Items') }}</h3>
                    @if ($order->items->count())
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50"><tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Product') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Quantity') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Unit Price') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Total') }}</th>
                            </tr></thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $item->product->name ?? '-' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $item->quantity }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">${{ number_format($item->unit_price, 2) }}</td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">${{ number_format($item->total_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-sm text-gray-500">{{ __('No items added yet.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
