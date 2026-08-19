<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
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
    <body class="bg-gray-50 text-gray-900 antialiased overflow-x-hidden">
        <div class="min-h-screen flex flex-col md:flex-row" x-data="{ sidebarOpen: false }">
            
            <!-- Sidebar (Dark Green / Emerald 900) -->
            <aside 
                class="fixed inset-y-0 left-0 z-50 w-64 bg-[#064e3b] text-white transition-transform duration-300 transform md:relative md:translate-x-0"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            >
                <!-- Close Sidebar Button (Mobile) -->
                <button @click="sidebarOpen = false" class="absolute top-4 right-4 md:hidden text-emerald-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>

                <div class="p-6">
                    <div class="flex items-center gap-3 mb-10">
                        <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-emerald-900/50">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-book-open text-white"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                        </div>
                        <div>
                            <h2 class="font-bold text-sm tracking-widest uppercase">Perpus</h2>
                            <p class="text-[10px] text-emerald-400 font-medium uppercase tracking-widest">Skarifta Dashboard</p>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <nav class="space-y-1.5">
                        @include('layouts.sidebar-navigation')
                    </nav>
                </div>
                
                <!-- Sidebar Footer -->
                <div class="absolute bottom-0 w-full p-6 bg-[#043327] border-t border-emerald-800/50">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-emerald-700 flex items-center justify-center text-[10px] font-bold">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <p class="text-xs font-bold truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-emerald-400 truncate">{{ ucfirst(Auth::user()->getRoleNames()->first() ?? 'User') }}</p>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 bg-gray-50">
                
                <!-- Top Header -->
                <header class="bg-white border-b border-gray-200 h-16 flex items-center px-4 md:px-8 sticky top-0 z-40">
                    <button @click="sidebarOpen = true" class="md:hidden p-2 text-gray-500 mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
                    </button>
                    
                    <div class="flex-1">
                        @isset($header)
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-emerald-600 uppercase tracking-widest">Panel</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300"><path d="m9 18 6-6-6-6"/></svg>
                                {{ $header }}
                            </div>
                        @endisset
                    </div>

                    <div class="flex items-center gap-4">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-xs font-bold text-gray-500 hover:text-red-500 transition-colors flex items-center gap-2">
                                <span>LOGOUT</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                            </button>
                        </form>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 p-4 md:p-8">
                    {{ $slot }}
                </main>
            </div>

            <!-- Overlay for Mobile Sidebar -->
            <div 
                x-show="sidebarOpen" 
                @click="sidebarOpen = false" 
                class="fixed inset-0 bg-black/50 z-40 md:hidden"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                style="display: none;"
            ></div>
        </div>
    </body>
</html>
