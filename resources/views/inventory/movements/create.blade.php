<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Record Stock Movement') }}</h2>
            <a href="{{ route('stock-movements.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('Back to Movements') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('stock-movements.store') }}" x-data="{ type: 'IN' }" class="space-y-8">
                @csrf

                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">{{ __('Movement Details') }}</h3>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <x-input-label for="movement_type" :value="__('Movement Type')" />
                                <select id="movement_type" name="movement_type" x-model="type" class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    <option value="IN">{{ __('Stock In') }}</option>
                                    <option value="OUT">{{ __('Stock Out') }}</option>
                                    <option value="TRANSFER">{{ __('Transfer') }}</option>
                                    <option value="ADJUSTMENT">{{ __('Adjustment') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('movement_type')" class="mt-1.5" />
                            </div>
                            <div>
                                <x-input-label for="product_id" :value="__('Product')" />
                                <select id="product_id" name="product_id" class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    <option value="">{{ __('Select Product') }}</option>
                                    @foreach (\App\Models\Product::where('status', 'active')->orderBy('name')->get() as $product)
                                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }} ({{ $product->sku }})</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('product_id')" class="mt-1.5" />
                            </div>

                            {{-- Warehouse (IN, OUT, ADJUSTMENT) --}}
                            <div x-show="type !== 'TRANSFER'">
                                <x-input-label for="warehouse_id" :value="__('Warehouse')" />
                                <select id="warehouse_id" name="warehouse_id" class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" :required="type !== 'TRANSFER'">
                                    <option value="">{{ __('Select Warehouse') }}</option>
                                    @foreach (\App\Models\Warehouse::where('status', 'active')->orderBy('name')->get() as $wh)
                                        <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }} ({{ $wh->code }})</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('warehouse_id')" class="mt-1.5" />
                            </div>

                            {{-- Transfer: From Warehouse --}}
                            <div x-show="type === 'TRANSFER'" x-cloak>
                                <x-input-label for="from_warehouse_id" :value="__('From Warehouse')" />
                                <select id="from_warehouse_id" name="from_warehouse_id" class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" :required="type === 'TRANSFER'">
                                    <option value="">{{ __('Select Source') }}</option>
                                    @foreach (\App\Models\Warehouse::where('status', 'active')->orderBy('name')->get() as $wh)
                                        <option value="{{ $wh->id }}" {{ old('from_warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('from_warehouse_id')" class="mt-1.5" />
                            </div>

                            {{-- Transfer: To Warehouse --}}
                            <div x-show="type === 'TRANSFER'" x-cloak>
                                <x-input-label for="to_warehouse_id" :value="__('To Warehouse')" />
                                <select id="to_warehouse_id" name="to_warehouse_id" class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" :required="type === 'TRANSFER'">
                                    <option value="">{{ __('Select Destination') }}</option>
                                    @foreach (\App\Models\Warehouse::where('status', 'active')->orderBy('name')->get() as $wh)
                                        <option value="{{ $wh->id }}" {{ old('to_warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('to_warehouse_id')" class="mt-1.5" />
                            </div>

                            <div>
                                <x-input-label for="quantity" :value="__('Quantity')" />
                                <x-text-input id="quantity" type="number" step="0.01" min="0.01" name="quantity" :value="old('quantity')" required class="mt-1.5" />
                                <x-input-error :messages="$errors->get('quantity')" class="mt-1.5" />
                            </div>

                            <div class="sm:col-span-2">
                                <x-input-label for="notes" :value="__('Notes')" />
                                <textarea id="notes" name="notes" rows="3" class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" :required="type === 'ADJUSTMENT'" placeholder="{{ __('Optional notes (required for adjustments)') }}">{{ old('notes') }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-1.5" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('stock-movements.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('Cancel') }}</a>
                    <x-primary-button>{{ __('Record Movement') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
