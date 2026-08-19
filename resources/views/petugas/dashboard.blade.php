<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Petugas Perpustakaan
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4">
            <p class="text-gray-600 mb-6">Selamat datang, <strong>{{ auth()->user()->name }}</strong>. Gunakan menu di bawah untuk mengelola anggota.</p>

            <!-- Statistics Row -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Member</p>
                    <p class="text-3xl font-black text-emerald-600">{{ $totalMembers }}</p>
                </div>
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Menunggu Approval</p>
                    <p class="text-3xl font-black text-amber-500">{{ $pendingCount }}</p>
                </div>
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Booking Aktif</p>
                    <p class="text-3xl font-black text-indigo-600">{{ $activeQueues->count() }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Quick Actions -->
                <div class="lg:col-span-1 space-y-4">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-2 mb-4">Navigasi Utama</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <a href="{{ route('petugas.member.approval') }}"
                           class="group p-4 bg-white rounded-[2rem] shadow-sm hover:shadow-xl transition-all border border-gray-100 flex items-center gap-4 active:scale-95">
                            <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-xl group-hover:bg-amber-100 transition-colors">⏳</div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-sm">Persetujuan Member</h3>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">Menunggu Aktivasi</p>
                            </div>
                        </a>

                        <a href="{{ route('petugas.users') }}"
                           class="group p-4 bg-white rounded-[2rem] shadow-sm hover:shadow-xl transition-all border border-gray-100 flex items-center gap-4 active:scale-95">
                            <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-xl group-hover:bg-blue-100 transition-colors">👥</div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-sm">Manajemen Anggota</h3>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">Data & Monitoring</p>
                            </div>
                        </a>

                        <a href="{{ route('petugas.circulation.loan') }}"
                           class="group p-4 bg-white rounded-[2rem] shadow-sm hover:shadow-xl transition-all border border-gray-100 flex items-center gap-4 active:scale-95">
                            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-xl group-hover:bg-emerald-100 transition-colors">📚</div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-sm">Mode Peminjaman</h3>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">Scan Kode Buku</p>
                            </div>
                        </a>

                        <a href="{{ route('petugas.circulation.return') }}"
                           class="group p-4 bg-white rounded-[2rem] shadow-sm hover:shadow-xl transition-all border border-gray-100 flex items-center gap-4 active:scale-95">
                            <div class="w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center text-xl group-hover:bg-emerald-600 transition-colors">✅</div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-sm">Mode Pengembalian</h3>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">Proses Kembali & Denda</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Active Bookings / Queues -->
                <div class="lg:col-span-2 space-y-4">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-2 mb-4 flex items-center justify-between">
                        Antrean Booking Siap Ambil
                        <span class="bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded-lg border border-emerald-100">{{ $activeQueues->count() }}</span>
                    </h3>

                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden min-h-[400px]">
                        @if($activeQueues->isEmpty())
                            <div class="flex flex-col items-center justify-center h-full py-20 text-center px-6">
                                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-200"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M8 7h6"/><path d="M8 11h6"/><path d="M8 15h6"/></svg>
                                </div>
                                <h4 class="font-bold text-gray-400">Belum Ada Antrean</h4>
                                <p class="text-xs text-gray-300 mt-1 max-w-[200px]">Semua member sedang santai atau belum ada buku yang siap diambil.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50/50 border-b border-gray-50">
                                        <tr>
                                            <th class="p-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Member</th>
                                            <th class="p-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Koleksi Buku</th>
                                            <th class="p-6 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                                            <th class="p-6 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @foreach($activeQueues as $q)
                                            <tr class="hover:bg-gray-50/50 transition-colors">
                                                <td class="p-6">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-9 h-9 bg-indigo-50 rounded-xl flex items-center justify-center text-xs font-black text-indigo-600 shadow-sm border border-indigo-100">
                                                            {{ substr($q->user->name, 0, 1) }}
                                                        </div>
                                                        <div>
                                                            <p class="font-bold text-gray-900 leading-tight">{{ $q->user->name }}</p>
                                                            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tight">{{ $q->user->member_type }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="p-6">
                                                    <p class="font-bold text-gray-900 leading-tight">{{ $q->book->title }}</p>
                                                    <p class="text-[9px] text-indigo-500 font-black uppercase tracking-wider mt-1">{{ $q->book->code }}</p>
                                                </td>
                                                <td class="p-6 text-center">
                                                    <span class="px-3 py-1.5 rounded-xl border {{ $q->status === 'CALLED' ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100' }} text-[9px] font-black uppercase tracking-widest">
                                                        {{ $q->status }}
                                                    </span>
                                                </td>
                                                <td class="p-6 text-right">
                                                    <a href="{{ route('petugas.circulation.loan', ['code' => $q->book->code, 'student_id' => $q->user_id]) }}" 
                                                       class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-600 text-white text-[9px] font-black rounded-xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-200 active:scale-95 uppercase tracking-widest">
                                                        Proses Pinjam
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
