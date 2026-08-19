@php
    $links = [];
    
    if (Auth::user()->hasRole('admin')) {
        $links = [
            ['name' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'layout-dashboard'],
            ['name' => 'Semua User', 'route' => 'admin.users.index', 'icon' => 'users'],
            ['name' => 'Persetujuan', 'route' => 'admin.pending.users', 'icon' => 'user-check'],
            ['name' => 'Moderasi Foto', 'route' => 'petugas.avatars.moderation', 'icon' => 'image'],
            ['name' => 'OTP Management', 'route' => 'admin.otp.index', 'icon' => 'key'],
            ['name' => 'Buku & Katalog', 'route' => 'admin.books.index', 'icon' => 'book'],
            ['name' => 'Peminjaman', 'route' => 'petugas.circulation.loan', 'icon' => 'arrow-up-right'],
            ['name' => 'Pengembalian', 'route' => 'petugas.circulation.return', 'icon' => 'arrow-down-left'],
            ['name' => 'Moderasi Ulasan', 'route' => 'admin.reviews.moderation', 'icon' => 'star-half'],
            ['name' => 'Monitoring Chat', 'route' => 'petugas.chats.monitoring.index', 'icon' => 'message-square'],
            ['name' => 'Master Label', 'route' => 'admin.label.master.index', 'icon' => 'tag'],
            ['name' => 'Jadwal Petugas', 'route' => 'admin.staff.schedule.index', 'icon' => 'calendar'],
        ];
    } elseif (Auth::user()->hasRole('petugas')) {
        $links = [
            ['name' => 'Dashboard', 'route' => 'petugas.dashboard', 'icon' => 'layout-dashboard'],
            ['name' => 'Member Approval', 'route' => 'petugas.member.approval', 'icon' => 'user-plus'],
            ['name' => 'Moderasi Foto', 'route' => 'petugas.avatars.moderation', 'icon' => 'image'],
            ['name' => 'Manajemen Buku', 'route' => 'petugas.books.index', 'icon' => 'book'],
            ['name' => 'Peminjaman', 'route' => 'petugas.circulation.loan', 'icon' => 'arrow-up-right'],
            ['name' => 'Pengembalian', 'route' => 'petugas.circulation.return', 'icon' => 'arrow-down-left'],
            ['name' => 'Moderasi Rangkuman', 'route' => 'petugas.summaries.moderation', 'icon' => 'book-check'],
            ['name' => 'Moderasi Ulasan', 'route' => 'petugas.reviews.moderation', 'icon' => 'star-half'],
            ['name' => 'Monitoring Chat', 'route' => 'petugas.chats.monitoring.index', 'icon' => 'message-square'],
            ['name' => 'Denda', 'route' => 'petugas.fines.index', 'icon' => 'circle-dollar-sign'],
        ];
    }
@endphp

@foreach($links as $link)
    @php
        $isActive = request()->routeIs($link['route'] . '*');
    @endphp
    <a 
        href="{{ route($link['route']) }}" 
        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ $isActive ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-900/40' : 'text-emerald-100/70 hover:bg-emerald-800/50 hover:text-white' }}"
    >
        <div class="{{ $isActive ? 'text-white' : 'text-emerald-400 group-hover:text-emerald-200' }}">
            @switch($link['icon'])
                @case('layout-dashboard')
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-dashboard"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                    @break
                @case('users')
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    @break
                @case('user-check')
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-check"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>
                    @break
                @case('key')
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-key"><path d="m21 2-2 2"/><circle cx="7.5" cy="15.5" r="5.5"/><path d="m21 2-9.6 9.6"/><path d="m15.5 7.5 3 3L22 7l-3-3"/><path d="m11 10 3 3"/></svg>
                    @break
                @case('book')
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-book"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M8 6h10"/><path d="M8 10h10"/><path d="M8 14h10"/></svg>
                    @break
                @case('tag')
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-tag"><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><path d="M7 7h.01"/></svg>
                    @break
                @case('calendar')
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                    @break
                @case('user-plus')
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-plus"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
                    @break
                @case('arrow-up-right')
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-right"><path d="M7 7h10v10"/><path d="M7 17 17 7"/></svg>
                    @break
                @case('arrow-down-left')
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-down-left"><path d="M17 17H7V7"/><path d="M17 7 7 17"/></svg>
                    @break
                @case('circle-dollar-sign')
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-dollar-sign"><circle cx="12" cy="12" r="10"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 18V6"/></svg>
                    @break
                @case('star-half')
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star-half"><path d="M12 17.8 5.8 21 7 14.1 2 9.3l7-1L12 2l3 6.3 7 1-5 4.8 1.2 6.9-6.2-3.3Z"/><path d="M12 2v15.8"/></svg>
                    @break
                @case('book-check')
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-book-check"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M18 6h2"/><path d="M18 10h2"/><path d="M18 14h2"/><path d="M8 6h3"/><path d="M8 10h3"/><path d="M8 14h3"/><path d="m9 17 2 2 4-4"/></svg>
                    @break
                @case('image')
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-image"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                    @break
                @case('message-square')
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    @break
            @endswitch
        </div>
        <span class="text-xs font-bold tracking-wide transition-colors">{{ $link['name'] }}</span>
        
        @if($isActive)
            <div class="ml-auto w-1.5 h-1.5 rounded-full bg-white animate-pulse"></div>
        @endif
    </a>
@endforeach
