<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Dashboards') }}</h2>
            <a href="{{ route('dashboards.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 transition ease-in-out duration-150">{{ __('Create Dashboard') }}</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Name') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Description') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Widgets') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Created') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($dashboards as $dashboard)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        <a href="{{ route('dashboards.show', $dashboard) }}" class="text-indigo-600 hover:text-indigo-500">{{ $dashboard->name }}</a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $dashboard->description ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $dashboard->widgets_count }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $dashboard->created_at->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <form method="POST" action="{{ route('dashboards.destroy', $dashboard) }}" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" onclick="return confirm('Delete this dashboard?')" class="text-red-600 hover:text-red-500">{{ __('Delete') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-6 py-12 text-center"><h3 class="text-sm font-medium text-gray-900">{{ __('No dashboards found') }}</h3></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($dashboards->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                            <p class="text-sm text-gray-700">{{ __('Showing') }} <span class="font-medium">{{ $dashboards->firstItem() }}</span> {{ __('to') }} <span class="font-medium">{{ $dashboards->lastItem() }}</span> {{ __('of') }} <span class="font-medium">{{ $dashboards->total() }}</span> {{ __('results') }}</p>
                            <div>{{ $dashboards->links() }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
