@props(['name' => 'password', 'label' => 'Password', 'required' => true])

@php
    $inputId = $attributes->get('id') ?? $name;
    $errorId = $inputId . '-error';
    $hintId = $inputId . '-hint';
@endphp

<div x-data="{
    password: '{{ old($name) }}',
    show: false,
    strength: 0,
    score: 0,
    label: '',
    color: '',
    checks: {
        length: false,
        uppercase: false,
        lowercase: false,
        number: false,
        special: false,
    },
    calculate() {
        this.score = 0;
        this.checks.length = this.password.length >= 8;
        this.checks.uppercase = /[A-Z]/.test(this.password);
        this.checks.lowercase = /[a-z]/.test(this.password);
        this.checks.number = /[0-9]/.test(this.password);
        this.checks.special = /[^A-Za-z0-9]/.test(this.password);

        if (this.checks.length) this.score++;
        if (this.checks.uppercase) this.score++;
        if (this.checks.lowercase) this.score++;
        if (this.checks.number) this.score++;
        if (this.checks.special) this.score++;

        if (this.score <= 2) {
            this.strength = 1;
            this.label = 'Weak';
            this.color = 'bg-red-500';
        } else if (this.score <= 3) {
            this.strength = 2;
            this.label = 'Fair';
            this.color = 'bg-orange-500';
        } else if (this.score <= 4) {
            this.strength = 3;
            this.label = 'Good';
            this.color = 'bg-yellow-500';
        } else {
            this.strength = 4;
            this.label = 'Strong';
            this.color = 'bg-emerald-500';
        }
    }
}" x-init="$watch('password', () => calculate())">

    <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700">
        {{ __($label) }}
        @if ($required)
            <span class="text-red-500" aria-hidden="true">*</span>
        @endif
    </label>

    <div class="mt-1.5 relative">
        <input
            :type="show ? 'text' : 'password'"
            id="{{ $inputId }}"
            name="{{ $name }}"
            x-model="password"
            @if ($required) required @endif
            autocomplete="new-password"
            placeholder="Create a strong password"
            {{ $attributes->merge([
                'class' => 'block w-full rounded-lg border-gray-300 pr-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
            ]) }}
            :aria-invalid="$errors->has($name)"
            :aria-describedby="collect([$errors->has($name) ? $errorId : null, $hintId])->filter()->implode(' ') ?: null"
        >
        <button
            type="button"
            @click="show = !show"
            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 rounded"
            :aria-label="show ? '{{ __('Hide password') }}' : '{{ __('Show password') }}'"
            tabindex="-1"
        >
            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
            </svg>
        </button>
    </div>

    {{-- Error --}}
    @if ($errors->has($name))
        <p id="{{ $errorId }}" class="flex items-center text-sm text-red-600 mt-1.5" role="alert">
            <svg class="h-4 w-4 shrink-0 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
            </svg>
            {{ $errors->first($name) }}
        </p>
    @endif

    {{-- Strength Indicator --}}
    <div x-show="password.length > 0" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="mt-3" role="status" aria-live="polite">
        {{-- Strength Bar --}}
        <div class="flex items-center gap-2">
            <div class="flex-1 flex gap-1">
                <template x-for="i in 4" :key="i">
                    <div
                        class="h-1.5 flex-1 rounded-full transition-colors duration-300"
                        :class="i <= strength ? color : 'bg-gray-200'"
                    ></div>
                </template>
            </div>
            <span
                class="text-xs font-medium shrink-0 min-w-[48px] text-right"
                :class="{
                    'text-red-600': strength === 1,
                    'text-orange-600': strength === 2,
                    'text-yellow-600': strength === 3,
                    'text-emerald-600': strength === 4,
                }"
                x-text="label"
            ></span>
        </div>

        {{-- Requirements Checklist --}}
        <div class="mt-2.5 grid grid-cols-2 gap-x-4 gap-y-1" id="{{ $hintId }}">
            <div class="flex items-center gap-1.5 text-xs" :class="checks.length ? 'text-emerald-600' : 'text-gray-400'">
                <svg x-show="checks.length" class="h-3.5 w-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                </svg>
                <svg x-show="!checks.length" class="h-3.5 w-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11.25a.75.75 0 00-1.5 0v2.5h-2.5a.75.75 0 000 1.5h2.5v2.5a.75.75 0 001.5 0v-2.5h2.5a.75.75 0 000-1.5h-2.5v-2.5z" />
                </svg>
                <span>8+ characters</span>
            </div>
            <div class="flex items-center gap-1.5 text-xs" :class="checks.uppercase ? 'text-emerald-600' : 'text-gray-400'">
                <svg x-show="checks.uppercase" class="h-3.5 w-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                </svg>
                <svg x-show="!checks.uppercase" class="h-3.5 w-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11.25a.75.75 0 00-1.5 0v2.5h-2.5a.75.75 0 000 1.5h2.5v2.5a.75.75 0 001.5 0v-2.5h2.5a.75.75 0 000-1.5h-2.5v-2.5z" />
                </svg>
                <span>Uppercase letter</span>
            </div>
            <div class="flex items-center gap-1.5 text-xs" :class="checks.number ? 'text-emerald-600' : 'text-gray-400'">
                <svg x-show="checks.number" class="h-3.5 w-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                </svg>
                <svg x-show="!checks.number" class="h-3.5 w-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11.25a.75.75 0 00-1.5 0v2.5h-2.5a.75.75 0 000 1.5h2.5v2.5a.75.75 0 001.5 0v-2.5h2.5a.75.75 0 000-1.5h-2.5v-2.5z" />
                </svg>
                <span>Number</span>
            </div>
            <div class="flex items-center gap-1.5 text-xs" :class="checks.special ? 'text-emerald-600' : 'text-gray-400'">
                <svg x-show="checks.special" class="h-3.5 w-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                </svg>
                <svg x-show="!checks.special" class="h-3.5 w-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11.25a.75.75 0 00-1.5 0v2.5h-2.5a.75.75 0 000 1.5h2.5v2.5a.75.75 0 001.5 0v-2.5h2.5a.75.75 0 000-1.5h-2.5v-2.5z" />
                </svg>
                <span>Special character</span>
            </div>
        </div>
    </div>
</div>
