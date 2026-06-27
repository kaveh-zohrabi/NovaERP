<x-guest-layout>
    {{-- Session Status --}}
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

    <form method="POST" action="{{ route('login') }}" class="space-y-5" aria-label="{{ __('Login form') }}">
        @csrf

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
                    autofocus
                    autocomplete="username"
                    :aria-invalid="$errors->has('email')"
                    :aria-describedby="$errors->has('email') ? 'email-error' : null"
                    placeholder="you@company.com"
                />
            </div>
            <x-input-error id="email-error" :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        {{-- Password --}}
        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Password')" />
                @if (Route::has('password.request'))
                    <a
                        href="{{ route('password.request') }}"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 rounded transition-colors"
                    >
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>
            <div class="mt-1.5">
                <x-password-input
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    :aria-invalid="$errors->has('password')"
                    :aria-describedby="$errors->has('password') ? 'password-error' : null"
                    placeholder="Enter your password"
                />
            </div>
            <x-input-error id="password-error" :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        {{-- Remember Me --}}
        <div class="flex items-center">
            <input
                id="remember_me"
                type="checkbox"
                name="remember"
                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0 cursor-pointer"
            >
            <label for="remember_me" class="ml-2 block text-sm text-gray-700 cursor-pointer select-none">
                {{ __('Remember me') }}
            </label>
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
                {{ __('Sign in') }}
            </button>
        </div>
    </form>

    @if (Route::has('register'))
        <x-slot name="footer">
            <p class="text-gray-600">
                {{ __('Don\'t have an account?') }}
                <a
                    href="{{ route('register') }}"
                    class="font-semibold text-indigo-600 hover:text-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 rounded transition-colors"
                >
                    {{ __('Create an account') }}
                </a>
            </p>
        </x-slot>
    @endif
</x-guest-layout>
