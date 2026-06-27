@props(['disabled' => false])

<button
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge([
        'type' => 'submit',
        'class' => 'inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-semibold shadow-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed ' .
            ($attributes->get('variant') === 'secondary'
                ? 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 focus:ring-indigo-500'
                : 'bg-indigo-600 text-white border border-transparent hover:bg-indigo-500 focus:ring-indigo-500'),
    ]) }}
>
    {{ $slot }}
</button>
