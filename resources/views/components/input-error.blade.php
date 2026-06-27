@props(['messages'])

@if ($messages)
    <div {{ $attributes->merge(['class' => '']) }} role="alert" aria-live="polite">
        @foreach ((array) $messages as $message)
            <p class="flex items-center text-sm text-red-600 mt-1">
                <svg class="h-4 w-4 shrink-0 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                </svg>
                {{ $message }}
            </p>
        @endforeach
    </div>
@endif
