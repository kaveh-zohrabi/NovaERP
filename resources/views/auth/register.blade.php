<x-guest-layout :title="__('Create your account')">
    <form method="POST" action="{{ route('register') }}" class="space-y-5" aria-label="{{ __('Registration form') }}">
        @csrf

        {{-- Name --}}
        <div>
            <x-input-label for="name" :value="__('Full name')" />
            <div class="mt-1.5">
                <x-text-input
                    id="name"
                    type="text"
                    name="name"
                    :value="old('name')"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="John Doe"
                    :aria-invalid="$errors->has('name')"
                    :aria-describedby="$errors->has('name') ? 'name-error' : null"
                />
            </div>
            <x-input-error id="name-error" :messages="$errors->get('name')" class="mt-1.5" />
        </div>

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('Email address')" />
            <div class="mt-1.5">
                <x-text-input
                    id="email"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autocomplete="username"
                    placeholder="you@company.com"
                    :aria-invalid="$errors->has('email')"
                    :aria-describedby="$errors->has('email') ? 'email-error' : null"
                />
            </div>
            <x-input-error id="email-error" :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        {{-- Password --}}
        <div>
            <x-password-strength
                name="password"
                label="Password"
                :required="true"
                id="password"
                :aria-invalid="$errors->has('password')"
            />
        </div>

        {{-- Confirm Password --}}
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm password')" />
            <div class="mt-1.5">
                <x-text-input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Repeat your password"
                    :aria-invalid="$errors->has('password_confirmation')"
                    :aria-describedby="$errors->has('password_confirmation') ? 'password-confirmation-error' : null"
                />
            </div>
            <x-input-error id="password-confirmation-error" :messages="$errors->get('password_confirmation')" class="mt-1.5" />

            {{-- Match indicator --}}
            <div
                x-data="{ match: false }"
                x-init="$watch('$refs.pw.value', () => { match = $refs.pw.value !== '' && $refs.pw.value === $refs.confirm.value })"
                x-show="old('password_confirmation', '') !== ''"
                class="mt-1.5"
            >
                <p
                    class="flex items-center text-xs"
                    :class="match ? 'text-emerald-600' : 'text-gray-400'"
                    x-text="match ? '{{ __('Passwords match') }}' : '{{ __('Passwords do not match') }}'"
                ></p>
            </div>
            <input type="hidden" x-ref="pw" value="{{ old('password') }}">
            <input type="hidden" x-ref="confirm" value="{{ old('password_confirmation') }}">
        </div>

        {{-- Terms --}}
        <div class="flex items-start">
            <input
                id="terms"
                type="checkbox"
                name="terms"
                required
                class="h-4 w-4 mt-0.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0 cursor-pointer"
            >
            <label for="terms" class="ml-2 block text-sm text-gray-600 cursor-pointer select-none">
                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                    'terms_of_service' => '<a href="#" class="font-medium text-indigo-600 hover:text-indigo-500 underline underline-offset-2">Terms of Service</a>',
                    'privacy_policy' => '<a href="#" class="font-medium text-indigo-600 hover:text-indigo-500 underline underline-offset-2">Privacy Policy</a>',
                ]) !!}
            </label>
        </div>

        {{-- Submit --}}
        <div>
            <button
                type="submit"
                class="flex w-full justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-indigo-700 transition-all duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <svg class="mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M5.25 12a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM5.25 6a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5A.75.75 0 015.25 6zM5.25 18a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75z" />
                    <path fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z" clip-rule="evenodd" />
                </svg>
                {{ __('Create account') }}
            </button>
        </div>
    </form>

    <x-slot name="footer">
        <p class="text-gray-600">
            {{ __('Already have an account?') }}
            <a
                href="{{ route('login') }}"
                class="font-semibold text-indigo-600 hover:text-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 rounded transition-colors"
            >
                {{ __('Sign in') }}
            </a>
        </p>
    </x-slot>
</x-guest-layout>
