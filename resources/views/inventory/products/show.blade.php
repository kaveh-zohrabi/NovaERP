<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $product->name }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('Edit') }}</a>
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('Back to List') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div><x-input-label :value="__('Name')" /><p class="mt-1 text-sm text-gray-900">{{ $product->name }}</p></div>
                        <div><x-input-label :value="__('SKU')" /><p class="mt-1 text-sm text-gray-900">{{ $product->sku }}</p></div>
                        <div><x-input-label :value="__('Category')" /><p class="mt-1 text-sm text-gray-900">{{ $product->category->name ?? '-' }}</p></div>
                        <div><x-input-label :value="__('Status')" /><p class="mt-1"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $product->status === 'active' ? __('Active') : __('Inactive') }}</span></p></div>
                        <div><x-input-label :value="__('Cost Price')" /><p class="mt-1 text-sm text-gray-900">${{ number_format($product->cost_price, 2) }}</p></div>
                        <div><x-input-label :value="__('Selling Price')" /><p class="mt-1 text-sm text-gray-900">${{ number_format($product->selling_price, 2) }}</p></div>
                        @if ($product->description)
                            <div class="sm:col-span-2"><x-input-label :value="__('Description')" /><p class="mt-1 text-sm text-gray-900">{{ $product->description }}</p></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
