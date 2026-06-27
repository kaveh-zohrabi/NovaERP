@props(['status'])

@if ($status)
    <div
        {{ $attributes->merge([
            'class' => 'rounded-lg bg-emerald-50 p-4',
            'role' => 'alert',
            'aria-live' => 'polite',
        ]) }}
    >
        <div class="flex items-center">
            <svg class="h-5 w-5 text-emerald-500 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
            </svg>
            <p class="ml-3 text-sm font-medium text-emerald-800">{{ $status }}</p>
        </div>
    </div>
@endif
