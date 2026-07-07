<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $folder->name }}</h2>
            <a href="{{ route('folders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">{{ __('Back to Folders') }}</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($folder->description)
                <p class="text-sm text-gray-600">{{ $folder->description }}</p>
            @endif

            @if ($children->isNotEmpty())
                <div>
                    <h3 class="font-medium text-gray-900 mb-3">{{ __('Subfolders') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        @foreach ($children as $child)
                            <a href="{{ route('folders.show', $child) }}" class="bg-white border rounded-lg p-3 hover:shadow-md transition-shadow">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>
                                    <span class="text-sm text-gray-900">{{ $child->name }}</span>
                                    <span class="text-xs text-gray-400">{{ $child->documents_count }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b">
                    <h3 class="font-medium text-gray-900">{{ __('Files') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('File Name') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Size') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($documents as $doc)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        <a href="{{ route('documents.show', $doc) }}" class="text-indigo-600 hover:text-indigo-500">{{ $doc->original_name }}</a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $doc->formattedFileSize() }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="{{ route('documents.download', $doc) }}" class="text-green-600 hover:text-green-500">{{ __('Download') }}</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500">{{ __('No files in this folder') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($documents->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                            <p class="text-sm text-gray-700">{{ __('Showing') }} <span class="font-medium">{{ $documents->firstItem() }}</span> {{ __('to') }} <span class="font-medium">{{ $documents->lastItem() }}</span> {{ __('of') }} <span class="font-medium">{{ $documents->total() }}</span> {{ __('results') }}</p>
                            <div>{{ $documents->links() }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
