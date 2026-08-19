<x-member-layout>
    <div class="pb-36 min-h-screen bg-slate-50/60">
        <!-- Leaderboard Header -->
        <header class="bg-white border-b border-gray-100 px-6 py-6 flex flex-col items-center text-center shadow-xs" style="background-color: #ffffff !important; backdrop-filter: none !important; -webkit-backdrop-filter: none !important; opacity: 1 !important;">
            <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-500 mb-3 shadow-xs border border-amber-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trophy"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.45 1-1 1H4v2h16v-2h-5c-.55 0-1-.45-1-1v-2.34"/><path d="M12 2a7.7 7.7 0 0 1 7.54 8H4.46A7.7 7.7 0 0 1 12 2z"/></svg>
            </div>
            <h1 class="text-2xl font-black text-gray-900 leading-tight tracking-tight">Papan Peringkat Membaca</h1>
            <p class="text-xs text-gray-500 font-semibold uppercase tracking-widest mt-1.5">Top Siswa Teraktif Membaca Buku (Klik untuk lihat profil)</p>
        </header>

        <!-- Leaderboard List -->
        <div class="max-w-4xl mx-auto px-4 mt-6">
            <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden p-6 md:p-8">
                <!-- Top 3 Showcase Grid (Visual Podiums) -->
                @if($leaderboard->count() > 0)
                    <div class="flex flex-col sm:flex-row items-end justify-center gap-6 mb-10 mt-4 border-b border-gray-100 pb-10">
                        <!-- 2nd Place -->
                        @if($leaderboard->count() > 1)
                            <a href="{{ route('profile.view', $leaderboard[1]) }}" class="flex flex-col items-center order-2 sm:order-1 flex-1 max-w-[140px] text-center group cursor-pointer active:scale-95 transition-transform">
                                <div class="relative mb-2">
                                    <div class="w-16 h-16 rounded-full border-4 border-slate-100 overflow-hidden shadow-md group-hover:ring-4 group-hover:ring-emerald-100 transition-all bg-white">
                                        <img src="{{ $leaderboard[1]->avatar_url }}" class="w-full h-full object-cover">
                                    </div>
                                    <span class="absolute -top-2 -right-1 w-6 h-6 rounded-full bg-slate-400 text-white font-black text-xs flex items-center justify-center border-2 border-white shadow-xs">2</span>
                                </div>
                                <h4 class="text-xs font-black text-gray-900 line-clamp-1 group-hover:text-emerald-600 transition-colors">{{ $leaderboard[1]->name }}</h4>
                                <p class="text-[9px] font-bold text-gray-400 truncate mt-0.5">{{ $leaderboard[1]->kelas }}</p>
                                <div class="mt-2 px-3 py-1 bg-slate-50 text-slate-700 rounded-full text-[10px] font-black group-hover:bg-emerald-50 group-hover:text-emerald-700 transition-colors">{{ $leaderboard[1]->borrows_count }} Buku</div>
                            </a>
                        @endif

                        <!-- 1st Place (Center Podium, Larger) -->
                        <a href="{{ route('profile.view', $leaderboard[0]) }}" class="flex flex-col items-center order-1 sm:order-2 flex-1 max-w-[160px] text-center transform -translate-y-2 group cursor-pointer active:scale-95 transition-transform">
                            <div class="relative mb-3">
                                <div class="w-20 h-20 rounded-full border-4 border-amber-200 overflow-hidden shadow-lg ring-4 ring-amber-50 group-hover:ring-emerald-200 transition-all bg-white">
                                    <img src="{{ $leaderboard[0]->avatar_url }}" class="w-full h-full object-cover">
                                </div>
                                <span class="absolute -top-3 left-1/2 -translate-x-1/2 text-2xl animate-bounce">👑</span>
                                <span class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-amber-500 text-white font-black text-sm flex items-center justify-center border-2 border-white shadow-md">1</span>
                            </div>
                            <h4 class="text-sm font-black text-gray-900 line-clamp-1 group-hover:text-emerald-600 transition-colors">{{ $leaderboard[0]->name }}</h4>
                            <p class="text-[10px] font-bold text-gray-400 truncate mt-0.5">{{ $leaderboard[0]->kelas }}</p>
                            <div class="mt-2.5 px-4 py-1 bg-amber-50 text-amber-700 rounded-full text-xs font-black border border-amber-100 group-hover:bg-emerald-50 group-hover:text-emerald-700 transition-colors">{{ $leaderboard[0]->borrows_count }} Buku</div>
                        </a>

                        <!-- 3rd Place -->
                        @if($leaderboard->count() > 2)
                            <a href="{{ route('profile.view', $leaderboard[2]) }}" class="flex flex-col items-center order-3 flex-1 max-w-[140px] text-center group cursor-pointer active:scale-95 transition-transform">
                                <div class="relative mb-2">
                                    <div class="w-16 h-16 rounded-full border-4 border-orange-100 overflow-hidden shadow-md group-hover:ring-4 group-hover:ring-emerald-100 transition-all bg-white">
                                        <img src="{{ $leaderboard[2]->avatar_url }}" class="w-full h-full object-cover">
                                    </div>
                                    <span class="absolute -top-2 -right-1 w-6 h-6 rounded-full bg-orange-400 text-white font-black text-xs flex items-center justify-center border-2 border-white shadow-xs">3</span>
                                </div>
                                <h4 class="text-xs font-black text-gray-900 line-clamp-1 group-hover:text-emerald-600 transition-colors">{{ $leaderboard[2]->name }}</h4>
                                <p class="text-[9px] font-bold text-gray-400 truncate mt-0.5">{{ $leaderboard[2]->kelas }}</p>
                                <div class="mt-2 px-3 py-1 bg-orange-50/50 text-orange-700 rounded-full text-[10px] font-black group-hover:bg-emerald-50 group-hover:text-emerald-700 transition-colors">{{ $leaderboard[2]->borrows_count }} Buku</div>
                            </a>
                        @endif
                    </div>
                @endif

                <!-- Leaderboard Table -->
                <div class="border border-slate-100 rounded-2xl overflow-hidden shadow-xs">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="py-3 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center w-16">Peringkat</th>
                                <th class="py-3 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Lengkap</th>
                                <th class="py-3 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kelas & Jurusan</th>
                                <th class="py-3 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center w-28">Buku Dibaca</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaderboard as $index => $topUser)
                                <tr class="border-b border-slate-100/60 hover:bg-emerald-50/30 transition-colors cursor-pointer" onclick="window.location='{{ route('profile.view', $topUser) }}'">
                                    <td class="py-4 px-4 text-center">
                                        @if($index === 0)
                                            <span class="text-lg">🥇</span>
                                        @elseif($index === 1)
                                            <span class="text-lg">🥈</span>
                                        @elseif($index === 2)
                                            <span class="text-lg">🥉</span>
                                        @else
                                            <span class="text-xs font-black text-gray-400">{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4">
                                        <a href="{{ route('profile.view', $topUser) }}" class="flex items-center gap-3 group">
                                            <div class="w-9 h-9 rounded-full overflow-hidden border border-gray-100 shrink-0 bg-white">
                                                <img src="{{ $topUser->avatar_url }}" class="w-full h-full object-cover">
                                            </div>
                                            <div>
                                                <span class="text-xs font-black text-gray-900 group-hover:text-emerald-600 transition-colors block">{{ $topUser->name }}</span>
                                                <span class="text-[9px] text-gray-400 font-bold group-hover:text-emerald-500">Lihat Profil ↗</span>
                                            </div>
                                        </a>
                                    </td>
                                    <td class="py-4 px-4 text-xs font-bold text-gray-500">
                                        {{ $topUser->kelas ?? '-' }} ({{ $topUser->jurusan ?? 'Umum' }})
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-black">
                                            {{ $topUser->borrows_count }} Buku
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-12 text-center text-xs font-bold text-gray-400">
                                        Belum ada aktivitas membaca buku di sekolah.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-member-layout>
