<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Skarifta Perpus') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Outfit', sans-serif; }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="bg-[#f8faff] text-gray-900 antialiased overflow-x-hidden">
        <div class="min-h-screen flex flex-col items-center justify-center p-6 relative">
            
            <!-- Dynamic Background Decorations -->
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-indigo-100/50 rounded-full blur-[100px] -z-10"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-100/50 rounded-full blur-[100px] -z-10"></div>

            <div class="w-full max-w-[420px] animate-fade-in-up">
                {{ $slot }}
            </div>

            <div class="mt-12 text-center space-y-2">
                <p class="text-[11px] text-gray-400 font-medium uppercase tracking-[0.2em]">&copy; {{ date('Y') }} Skarifta Perpus. Advanced Digital Library.</p>
                <p class="text-[9px] text-gray-400/80 font-bold uppercase tracking-[0.2em]">Created by Ghozy Argana</p>
            </div>

        </div>
    </body>
</html>