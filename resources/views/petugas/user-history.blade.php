<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('petugas.users') }}" class="p-2 hover:bg-gray-100 rounded-xl transition-colors text-gray-400 hover:text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            </a>
            <h2 class="font-bold text-lg text-gray-800 leading-tight">Detail Aktivitas member</h2>
        </div>
    </x-slot>

    <div class="py-12 space-y-8">
        <!-- Profil Ringkas member -->
        <div class="max-w-7xl mx-auto px-6">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-600 to-emerald-800 p-8 text-white">
                    <div class="flex flex-wrap items-center justify-between gap-6">
                        <div class="flex items-center gap-6">
                            <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-[2rem] flex items-center justify-center text-4xl font-black border border-white/20">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <h1 class="text-2xl font-black tracking-tight">{{ $user->name }}</h1>
                                <p class="text-emerald-100/80 font-bold uppercase tracking-widest text-[10px]">{{ $user->email }} • {{ $user->member_type }}</p>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="px-3 py-1 bg-white/10 backdrop-blur-sm rounded-full text-[9px] font-black uppercase tracking-widest border border-white/10">ID: {{ $user->id }}</span>
                                    <span class="px-3 py-1 bg-white/10 backdrop-blur-sm rounded-full text-[9px] font-black uppercase tracking-widest border border-white/10">{{ $user->kelas ?? '-' }} {{ $user->jurusan ?? '' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-4">
                             <div class="bg-white/10 backdrop-blur-sm p-4 rounded-3xl border border-white/10 text-center min-w-[100px]">
                                 <p class="text-[8px] font-black uppercase tracking-widest text-emerald-200 mb-1">Total Pinjam</p>
                                 <p class="text-xl font-black">{{ $borrows->total() }}</p>
                             </div>
                             <div class="bg-white/10 backdrop-blur-sm p-4 rounded-3xl border border-white/10 text-center min-w-[100px]">
                                 <p class="text-[8px] font-black uppercase tracking-widest text-emerald-200 mb-1">Aktif Antrean</p>
                                 <p class="text-xl font-black">{{ $queues->count() }}</p>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sidebar: Antrean Aktif -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-600"><path d="M12 2v10l4.5 4.5"/><circle cx="12" cy="12" r="10"/></svg>
                        Antrean Saat Ini
                    </h3>
                    <div class="space-y-4">
                        @forelse($queues as $q)
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 relative overflow-hidden group">
                                <div class="absolute top-0 right-0 p-2 opacity-5 animate-pulse">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/></svg>
                                </div>
                                <p class="text-[11px] font-black text-gray-900 line-clamp-1 mb-1">{{ $q->book->title }}</p>
                                <div class="flex items-center justify-between mt-3 text-[9px] font-bold text-gray-400 uppercase tracking-wider">
                                    <span>Status: {{ $q->status }}</span>
                                    <span>{{ $q->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 font-medium text-center py-8">Tidak ada antrean aktif.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Main: Riwayat Peminjaman -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/30">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-600"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M8 7h6"/><path d="M8 11h6"/><path d="M8 15h6"/></svg>
                            Log Peminjaman Koleksi
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left border-b border-gray-50">
                                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Koleksi Buku</th>
                                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Pinjam</th>
                                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Kembali</th>
                                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($borrows as $b)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="p-6">
                                            <p class="font-bold text-gray-900 leading-tight">{{ $b->book->title }}</p>
                                            <p class="text-[9px] text-indigo-500 font-black uppercase mt-1 tracking-wider">{{ $b->book->code }}</p>
                                        </td>
                                        <td class="p-6 text-center">
                                            <p class="text-xs font-bold text-gray-700">{{ $b->borrow_date->format('d/m/Y') }}</p>
                                            <p class="text-[9px] text-gray-400 font-bold uppercase mt-0.5">{{ $b->borrow_date->format('H:i') }}</p>
                                        </td>
                                        <td class="p-6 text-center">
                                            @if($b->return_date)
                                                <p class="text-xs font-bold text-emerald-600">{{ $b->return_date->format('d/m/Y') }}</p>
                                                <p class="text-[9px] text-emerald-400 font-bold uppercase mt-0.5">{{ $b->return_date->format('H:i') }}</p>
                                            @else
                                                <span class="text-[9px] font-black text-amber-500 uppercase tracking-widest">—</span>
                                            @endif
                                        </td>
                                        <td class="p-6 text-right">
                                            @php
                                                $sMap = [
                                                    'active'   => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                    'late'     => 'bg-red-50 text-red-600 border-red-100',
                                                    'returned' => 'bg-gray-50 text-gray-400 border-gray-100',
                                                ];
                                                $cls = $sMap[$b->status] ?? 'bg-gray-100 text-gray-500';
                                            @endphp
                                            <span class="px-3 py-1.5 rounded-xl border {{ $cls }} text-[9px] font-black uppercase tracking-widest">
                                                {{ $b->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="p-12 text-center text-gray-300 font-bold italic">Member ini belum pernah meminjam buku.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-8 border-t border-gray-50 bg-gray-50/20">
                        {{ $borrows->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
