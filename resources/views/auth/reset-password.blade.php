<x-guest-layout :title="__('Create new password')">
    <p class="text-sm text-gray-600 mb-6">
        {{ __('Enter your new password below. Make sure it\'s at least 8 characters long.') }}
    </p>

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 p-4" role="alert" aria-live="polite">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-red-500 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                </svg>
                <p class="ml-3 text-sm font-medium text-red-800">{{ $errors->first() }}</p>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5" aria-label="{{ __('Reset password form') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('Email address')" />
            <div class="mt-1.5">
                <x-text-input
                    id="email"
                    type="email"
                    name="email"
                    :value="old('email', $request->email)"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="you@company.com"
                    :aria-invalid="$errors->has('email')"
                    :aria-describedby="$errors->has('email') ? 'email-error' : null"
                />
            </div>
            <x-input-error id="email-error" :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        {{-- New Password --}}
        <div>
            <x-password-strength
                name="password"
                label="New password"
                :required="true"
                id="password"
                :aria-invalid="$errors->has('password')"
            />
        </div>

        {{-- Confirm Password --}}
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm new password')" />
            <div class="mt-1.5">
                <x-text-input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Repeat your new password"
                    :aria-invalid="$errors->has('password_confirmation')"
                    :aria-describedby="$errors->has('password_confirmation') ? 'password-confirmation-error' : null"
                />
            </div>
            <x-input-error id="password-confirmation-error" :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        {{-- Submit --}}
        <div>
            <button
                type="submit"
                class="flex w-full justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-indigo-700 transition-all duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <svg class="mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                </svg>
                {{ __('Reset password') }}
            </button>
        </div>
    </form>

    <x-slot name="footer">
        <p class="text-gray-600">
            {{ __('Remember your password?') }}
            <a
                href="{{ route('login') }}"
                class="font-semibold text-indigo-600 hover:text-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 rounded transition-colors"
            >
                {{ __('Back to sign in') }}
            </a>
        </p>
    </x-slot>
</x-guest-layout>
