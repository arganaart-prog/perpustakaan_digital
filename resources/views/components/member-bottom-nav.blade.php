<nav class="fixed bottom-0 left-0 right-0 bg-white/80 backdrop-blur-md border-t border-gray-100 px-6 py-3 flex items-center justify-between z-50">
    <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-gray-400' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-grid"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
        <span class="text-[10px] font-medium tracking-wide">Dashboard</span>
    </a>

    <a href="{{ route('member.books.index') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('member.books.index') ? 'text-indigo-600' : 'text-gray-400' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bookmark-check"><path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2Z"/><path d="m9 10 2 2 4-4"/></svg>
        <span class="text-[10px] font-medium tracking-wide">Catalog</span>
    </a>

    <a href="{{ route('member.loans') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('member.loans') ? 'text-indigo-600' : 'text-gray-400' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-book-open-check"><path d="M12 21V7"/><path d="m16 12 2 2 4-4"/><path d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3.97 3.97 0 0 1 2 1 3.97 3.97 0 0 1 2-1h1"/><path d="m22 10-4 4-2-2"/></svg>
        <span class="text-[10px] font-medium tracking-wide">My Loans</span>
    </a>

    <a href="{{ route('member.leaderboard') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('member.leaderboard') ? 'text-indigo-600' : 'text-gray-400' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trophy"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.45 1-1 1H4v2h16v-2h-5c-.55 0-1-.45-1-1v-2.34"/><path d="M12 2a7.7 7.7 0 0 1 7.54 8H4.46A7.7 7.7 0 0 1 12 2z"/></svg>
        <span class="text-[10px] font-medium tracking-wide">Leaderboard</span>
    </a>
</nav>
