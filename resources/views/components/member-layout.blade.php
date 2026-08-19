<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
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
            .hide-scrollbar::-webkit-scrollbar { display: none; }
            .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
            .lock-scroll { overflow: hidden !important; }
        </style>
    </head>
    <body class="bg-gray-50 text-gray-900 antialiased pb-24">
        <!-- Header -->
        <header class="sticky top-0 z-40 bg-white border-b border-gray-100 shadow-xs" style="background-color: #ffffff !important; backdrop-filter: none !important; -webkit-backdrop-filter: none !important; opacity: 1 !important;">
            <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-book"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M8 6h10"/><path d="M8 10h10"/><path d="M8 14h10"/></svg>
                    </div>
                    <div>
                        <h1 class="text-sm font-bold tracking-tight text-gray-900 leading-none">{{ config('app.name') }}</h1>
                        <p class="text-[10px] text-gray-500 font-medium uppercase mt-0.5">Academic Journey</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <!-- Chat Icon Button in Header -->
                    @php
                        $unreadChatCount = \App\Models\ChatMessage::where('receiver_id', auth()->id())->where('is_read', false)->count();
                    @endphp
                    <a href="{{ route('member.chats.index') }}" class="w-8 h-8 flex items-center justify-center bg-gray-50 text-gray-600 rounded-full hover:bg-emerald-50 hover:text-emerald-600 active:scale-95 transition-all relative" title="Pesan & Obrolan">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        @if($unreadChatCount > 0)
                            <span class="absolute -top-1 -right-1 px-1.5 py-0.2 bg-emerald-600 text-white rounded-full text-[8px] font-black border border-white animate-pulse">
                                {{ $unreadChatCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Alerts Icon Button in Header -->
                    <a href="{{ route('member.notifications') }}" class="w-8 h-8 flex items-center justify-center bg-gray-50 text-gray-600 rounded-full hover:bg-emerald-50 hover:text-emerald-600 active:scale-95 transition-all relative" title="Notifikasi">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border border-white animate-pulse"></span>
                        @endif
                    </a>

                    <a href="{{ route('profile.edit') }}" class="w-8 h-8 bg-gray-200 rounded-full overflow-hidden border border-gray-100 hover:scale-105 active:scale-95 transition-all block">
                        <img src="{{ Auth::user()->avatar_url }}" alt="avatar" class="w-full h-full object-cover">
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="w-8 h-8 flex items-center justify-center bg-red-50 text-red-600 rounded-lg active:scale-90 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto">
            {{ $slot }}
        </main>


        <!-- Bottom Nav -->
        <x-member-bottom-nav />
        
        <!-- Modals and Portals -->
        @stack('modals')
    </body>
</html>
