<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        {{-- This correctly includes your compiled CSS (with Tailwind) and JS --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        {{-- Added dark mode background class here --}}
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            {{-- Include navigation partial --}}
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow"> {{-- Added dark mode bg --}}
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{-- This correctly renders the header slot content --}}
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                {{-- This echoes the main content from your views using <x-app-layout> --}}
                {{ $slot }}
            </main>

        </div> {{-- End min-h-screen div --}}

        {{-- This renders scripts pushed using @push('scripts') in child views --}}
        @stack('scripts')
    </body>
</html>