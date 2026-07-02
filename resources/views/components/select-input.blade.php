@props(['disabled' => false, 'placeholder' => ''])

<select
    @disabled($disabled)
    {{ $attributes->merge([
        'class' => 'block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed',
    ]) }}
>
    @if ($placeholder)
        <option value="">{{ $placeholder }}</option>
    @endif
    {{ $slot }}
</select>
