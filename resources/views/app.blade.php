<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'School Events')</title> {{-- Allow setting page title --}}

    {{-- Fonts --}}
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    {{-- Styles (Example using Vite) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js']) {{-- Adjust if not using Vite --}}

    {{-- Or standard CSS link --}}
    {{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> --}}

    {{-- Add other head elements like custom CSS --}}
    @stack('styles') {{-- Placeholder for page-specific styles --}}

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm"> {{-- Example Bootstrap Navbar --}}
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                         <li class="nav-item">
                            <a class="nav-link" href="{{ route('events.index') }}">Events</a> {{-- Example link --}}
                        </li>
                        {{-- Add other nav links --}}
                    </ul>

                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                             {{-- Add Register link if needed --}}
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            {{-- This is the crucial part --}}
            @yield('content')
            {{-- Blade will inject the content from your @section('content') here --}}
        </main>

        <footer class="text-center mt-5">
             {{-- Add footer content if needed --}}
             <p>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}</p>
        </footer>
    </div>

    {{-- Add Javascript files if needed --}}
    {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}
    @stack('scripts') {{-- Placeholder for page-specific scripts --}}
</body>
</html>