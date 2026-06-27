<x-guest-layout :title="__('Reset your password')">
    <p class="text-sm text-gray-600 mb-6">
        {{ __('Enter your email address and we\'ll send you a link to reset your password.') }}
    </p>

    @if (session('status'))
        <div class="mb-4 rounded-lg bg-emerald-50 p-4" role="alert" aria-live="polite">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-emerald-500 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                </svg>
                <p class="ml-3 text-sm font-medium text-emerald-800">{{ session('status') }}</p>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5" aria-label="{{ __('Password reset request form') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email address')" />
            <div class="mt-1.5">
                <x-text-input
                    id="email"
                    type="email"
                    name="email"
                    :value="old('email')"
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

        <div>
            <button
                type="submit"
                class="flex w-full justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-indigo-700 transition-all duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <svg class="mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.311-.311h1.034a.75.75 0 000-1.5H4.598a.75.75 0 00-.75.75v3.634a.75.75 0 001.5 0v-.782l.312.311a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-7.424a5.5 5.5 0 00-9.201-2.466l-.311.311h1.034a.75.75 0 010 1.5H2.066a.75.75 0 01-.75-.75V2.404a.75.75 0 011.5 0v.782l.312-.311A7 7 0 0117.688 5.1a.75.75 0 01-1.449.39z" clip-rule="evenodd" />
                </svg>
                {{ __('Send reset link') }}
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
