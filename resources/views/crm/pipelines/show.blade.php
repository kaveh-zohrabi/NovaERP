<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Pipeline') }}: {{ $pipeline->name }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-medium text-gray-900 mb-4">{{ __('Stages') }}</h3>
                <div class="space-y-2">
                    @forelse ($pipeline->stages as $stage)
                        <div class="flex items-center justify-between border rounded-lg p-3">
                            <div class="flex items-center gap-3">
                                <span class="text-gray-400 text-sm">{{ $stage->sort_order }}</span>
                                <span class="text-sm font-medium text-gray-900">{{ $stage->name }}</span>
                                <span class="text-xs text-gray-500">{{ $stage->probability }}% probability</span>
                            </div>
                            <form method="POST" action="{{ route('pipelines.stages.destroy', $stage) }}">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Remove this stage?')" class="text-red-600 hover:text-red-500 text-xs">{{ __('Remove') }}</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">{{ __('No stages defined. Add one below.') }}</p>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('pipelines.stages.store', $pipeline) }}" class="mt-4 border-t pt-4">
                    @csrf
                    <div class="flex gap-2 items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">{{ __('Stage Name') }}</label>
                            <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>
                        <div class="w-24">
                            <label class="block text-sm font-medium text-gray-700">{{ __('Prob %') }}</label>
                            <input type="number" name="probability" value="0" min="0" max="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 transition ease-in-out duration-150">{{ __('Add') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
