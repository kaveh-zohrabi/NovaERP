<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $document->original_name }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('documents.download', $document) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 transition ease-in-out duration-150">{{ __('Download') }}</a>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($previewContent && $previewType)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-medium text-gray-900 mb-4">{{ __('Preview') }}</h3>
                    @if ($previewType === 'image')
                        <img src="data:{{ $document->mime_type }};base64,{{ $previewContent }}" alt="{{ $document->original_name }}" class="max-w-full h-auto rounded-lg">
                    @elseif ($previewType === 'text')
                        <pre class="bg-gray-50 rounded-lg p-4 text-sm overflow-auto max-h-96">{{ $previewContent }}</pre>
                    @endif
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-medium text-gray-900 mb-4">{{ __('File Details') }}</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div><dt class="font-medium text-gray-500">{{ __('File Name') }}</dt><dd class="mt-1 text-gray-900">{{ $document->file_name }}</dd></div>
                    <div><dt class="font-medium text-gray-500">{{ __('Original Name') }}</dt><dd class="mt-1 text-gray-900">{{ $document->original_name }}</dd></div>
                    <div><dt class="font-medium text-gray-500">{{ __('MIME Type') }}</dt><dd class="mt-1 text-gray-900">{{ $document->mime_type }}</dd></div>
                    <div><dt class="font-medium text-gray-500">{{ __('Size') }}</dt><dd class="mt-1 text-gray-900">{{ $document->formattedFileSize() }}</dd></div>
                    <div><dt class="font-medium text-gray-500">{{ __('Uploaded By') }}</dt><dd class="mt-1 text-gray-900">{{ $document->uploader?->name ?? '-' }}</dd></div>
                    <div><dt class="font-medium text-gray-500">{{ __('Uploaded At') }}</dt><dd class="mt-1 text-gray-900">{{ $document->created_at->format('M d, Y H:i') }}</dd></div>
                    <div><dt class="font-medium text-gray-500">{{ __('Folder') }}</dt><dd class="mt-1 text-gray-900">{{ $document->folder?->name ?? 'Root' }}</dd></div>
                    <div><dt class="font-medium text-gray-500">{{ __('Checksum') }}</dt><dd class="mt-1 text-gray-900 font-mono text-xs break-all">{{ $document->checksum }}</dd></div>
                </dl>
                @if ($document->description)
                    <div class="mt-4">
                        <dt class="font-medium text-gray-500 text-sm">{{ __('Description') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $document->description }}</dd>
                    </div>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-medium text-gray-900 mb-4">{{ __('Actions') }}</h3>
                <div class="flex flex-wrap gap-2">
                    <form method="POST" action="{{ route('documents.rename', $document) }}" class="inline-flex items-center gap-2">
                        @csrf @method('PATCH')
                        <input type="text" name="original_name" value="{{ $document->original_name }}" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <button type="submit" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">{{ __('Rename') }}</button>
                    </form>
                    <form method="POST" action="{{ route('documents.destroy', $document) }}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Move to trash?')" class="text-red-600 hover:text-red-500 text-sm font-medium">{{ __('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
