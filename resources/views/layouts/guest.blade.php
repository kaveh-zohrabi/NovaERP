<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="{{ config('app.name', 'Laravel') }} - Enterprise Resource Planning">

        <title>{{ config('app.name', 'Laravel') }} - Sign In</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="h-full font-sans antialiased bg-gradient-to-br from-slate-50 via-white to-slate-100">
        <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
            <div class="sm:mx-auto sm:w-full sm:max-w-md">
                <a href="/" class="flex justify-center focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 rounded-lg" aria-label="{{ config('app.name', 'Laravel') }} Home">
                    <x-application-logo class="h-16 w-auto text-indigo-600" />
                </a>
                <h1 class="mt-6 text-center text-2xl font-bold tracking-tight text-gray-900">
                    {{ $title ?? __('Sign in to your account') }}
                </h1>
                @if (isset($subtitle))
                    <p class="mt-2 text-center text-sm text-gray-600">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>

            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                <div class="bg-white py-8 px-4 shadow-lg sm:rounded-xl sm:px-10 border border-gray-200/60">
                    {{ $slot }}
                </div>

                @if (isset($footer))
                    <div class="mt-6 text-center text-sm text-gray-600">
                        {{ $footer }}
                    </div>
                @endif
            </div>
        </div>
    </body>
</html>
