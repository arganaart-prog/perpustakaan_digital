<nav class="fixed bottom-0 left-0 right-0 bg-white/90 backdrop-blur-md border-t border-gray-100 px-4 py-2.5 flex items-center justify-around z-50 shadow-lg">
    <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('dashboard') ? 'text-emerald-600 font-bold' : 'text-gray-400 hover:text-gray-600' }} transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
        <span class="text-[10px] tracking-wide">Beranda</span>
    </a>

    <a href="{{ route('member.books.index') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('member.books.index*') || request()->routeIs('member.books.detail*') ? 'text-emerald-600 font-bold' : 'text-gray-400 hover:text-gray-600' }} transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2Z"/><path d="m9 10 2 2 4-4"/></svg>
        <span class="text-[10px] tracking-wide">Katalog</span>
    </a>

    <a href="{{ route('member.loans') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('member.loans*') ? 'text-emerald-600 font-bold' : 'text-gray-400 hover:text-gray-600' }} transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21V7"/><path d="m16 12 2 2 4-4"/><path d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3.97 3.97 0 0 1 2 1 3.97 3.97 0 0 1 2-1h1"/><path d="m22 10-4 4-2-2"/></svg>
        <span class="text-[10px] tracking-wide">Pinjaman</span>
    </a>

    @php
        $bottomNavUnread = \App\Models\ChatMessage::where('receiver_id', auth()->id())->where('is_read', false)->count();
    @endphp
    <a href="{{ route('member.chats.index') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('member.chats*') ? 'text-emerald-600 font-bold' : 'text-gray-400 hover:text-gray-600' }} relative transition-colors">
        <div class="relative">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            @if($bottomNavUnread > 0)
                <span class="absolute -top-1.5 -right-2 px-1 py-0.2 bg-emerald-600 text-white rounded-full text-[8px] font-black border border-white">
                    {{ $bottomNavUnread }}
                </span>
            @endif
        </div>
        <span class="text-[10px] tracking-wide">Chat</span>
    </a>

    <a href="{{ route('member.leaderboard') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('member.leaderboard*') ? 'text-emerald-600 font-bold' : 'text-gray-400 hover:text-gray-600' }} transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.45 1-1 1H4v2h16v-2h-5c-.55 0-1-.45-1-1v-2.34"/><path d="M12 2a7.7 7.7 0 0 1 7.54 8H4.46A7.7 7.7 0 0 1 12 2z"/></svg>
        <span class="text-[10px] tracking-wide">Peringkat</span>
    </a>
</nav>
