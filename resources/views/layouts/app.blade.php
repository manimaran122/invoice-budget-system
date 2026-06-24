<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="font-sans antialiased text-app-dark">
        <div class="min-h-screen bg-app-background lg:pl-64">
            @include('layouts.navigation')

            <div class="min-h-screen">
                @isset($header)
                    <header class="bg-app-background">
                        <div class="max-w-7xl mx-auto pt-8 pb-4 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset
                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>

        <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @stack('scripts')
    </body>
</html>
