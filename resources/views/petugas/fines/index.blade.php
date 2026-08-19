<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Denda & Sanksi Keterlambatan
        </h2>
    </x-slot>

    <div x-data="{ tab: 'fines' }" class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold animate-fade-in flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Tab Switcher -->
            <div class="flex bg-slate-200/70 p-1.5 rounded-2xl max-w-md">
                <button 
                    @click="tab = 'fines'" 
                    :class="tab === 'fines' ? 'bg-white text-emerald-700 font-black shadow-xs' : 'text-gray-600 font-bold hover:text-gray-900'" 
                    class="flex-1 py-3 text-xs uppercase tracking-wider rounded-xl transition-all flex items-center justify-center gap-2"
                >
                    <span>💰 Denda Finansial ({{ $fines->count() }})</span>
                </button>
                <button 
                    @click="tab = 'social'" 
                    :class="tab === 'social' ? 'bg-white text-amber-700 font-black shadow-xs' : 'text-gray-600 font-bold hover:text-gray-900'" 
                    class="flex-1 py-3 text-xs uppercase tracking-wider rounded-xl transition-all flex items-center justify-center gap-2"
                >
                    <span>🧹 Hukuman Sosial ({{ $socialPunishments->count() }})</span>
                </button>
            </div>

            <!-- TAB 1: DENDA FINANSIAL (KETERLAMBATAN & KEHILANGAN) -->
            <div x-show="tab === 'fines'" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 overflow-hidden space-y-4">
                <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                    <div>
                        <h3 class="font-black text-gray-900 text-base">Daftar Denda & Verifikasi Bukti Bayar</h3>
                        <p class="text-xs text-gray-500 font-medium">Verifikasi pembayaran tunai (cash), transfer bank, dan periksa surat keterangan sakit.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="py-3 px-4 font-black text-gray-400 uppercase tracking-widest">Siswa / Peminjam</th>
                                <th class="py-3 px-4 font-black text-gray-400 uppercase tracking-widest">Buku</th>
                                <th class="py-3 px-4 font-black text-gray-400 uppercase tracking-widest">Tipe & Nominal</th>
                                <th class="py-3 px-4 font-black text-gray-400 uppercase tracking-widest">Metode & Bukti</th>
                                <th class="py-3 px-4 font-black text-gray-400 uppercase tracking-widest">Alasan / Surat Dokter</th>
                                <th class="py-3 px-4 font-black text-gray-400 uppercase tracking-widest text-center w-36">Aksi Pelunasan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($fines as $fine)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <!-- Siswa -->
                                    <td class="py-4 px-4 font-bold text-gray-900">
                                        {{ $fine->user->name }}
                                        <span class="block text-[10px] font-normal text-gray-400">{{ $fine->user->kelas ?? '-' }} ({{ $fine->user->jurusan ?? '-' }})</span>
                                    </td>

                                    <!-- Buku -->
                                    <td class="py-4 px-4">
                                        <span class="font-bold text-gray-800 block truncate max-w-[180px]">{{ $fine->book->title }}</span>
                                        <span class="font-mono text-[10px] text-gray-400">{{ $fine->book->code }}</span>
                                    </td>

                                    <!-- Tipe & Nominal -->
                                    <td class="py-4 px-4">
                                        @if($fine->fine_type === 'lost')
                                            <span class="px-2 py-0.5 bg-red-100 text-red-800 rounded-md text-[9px] font-black uppercase tracking-wider block w-max mb-1">Ganti Rugi Hilang</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-md text-[9px] font-black uppercase tracking-wider block w-max mb-1">Keterlambatan</span>
                                        @endif
                                        <span class="font-black text-sm text-red-600">Rp {{ number_format($fine->fine, 0, ',', '.') }}</span>
                                    </td>

                                    <!-- Metode & Bukti Bayar -->
                                    <td class="py-4 px-4">
                                        <div class="space-y-1">
                                            @if($fine->payment_method === 'transfer')
                                                <span class="px-2 py-0.5 bg-sky-100 text-sky-800 rounded-md text-[9px] font-black uppercase tracking-wider inline-block">Transfer Bank</span>
                                                @if($fine->payment_proof)
                                                    <a href="{{ route('petugas.borrows.view-file', [$fine, 'proof']) }}" target="_blank" class="block text-[10px] text-indigo-600 font-bold underline hover:text-indigo-800">
                                                        Lihat Struk Transfer ↗
                                                    </a>
                                                @else
                                                    <span class="text-[10px] text-gray-400 italic block">Belum upload struk</span>
                                                @endif
                                            @elseif($fine->payment_method === 'cash')
                                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-md text-[9px] font-black uppercase tracking-wider inline-block">Tunai (Cash)</span>
                                            @else
                                                <span class="text-[10px] text-gray-400 italic block">Belum pilih metode</span>
                                            @endif

                                            @if($fine->payment_status === 'paid')
                                                <span class="px-2 py-0.5 bg-emerald-500 text-white rounded text-[8px] font-black uppercase block w-max">Lunas</span>
                                            @elseif($fine->payment_status === 'pending_verification')
                                                <span class="px-2 py-0.5 bg-amber-500 text-white rounded text-[8px] font-black uppercase block w-max">Menunggu Cek</span>
                                            @else
                                                <span class="px-2 py-0.5 bg-slate-200 text-slate-700 rounded text-[8px] font-black uppercase block w-max">Belum Bayar</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Alasan & Surat Dokter -->
                                    <td class="py-4 px-4">
                                        <div class="max-w-[200px] space-y-1">
                                            @if($fine->late_reason)
                                                <p class="text-gray-700 italic font-medium">"{{ $fine->late_reason }}"</p>
                                            @else
                                                <span class="text-gray-400 italic text-[10px]">Tanpa catatan</span>
                                            @endif

                                            @if($fine->late_evidence)
                                                <a href="{{ route('petugas.borrows.view-file', [$fine, 'evidence']) }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] text-rose-600 font-bold underline hover:text-rose-800">
                                                    <span>📄 Surat Dokter / Bukti ↗</span>
                                                </a>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Aksi Pelunasan -->
                                    <td class="py-4 px-4 text-center">
                                        @if($fine->payment_status === 'paid' || $fine->fine_paid_at)
                                            <span class="text-xs font-black text-emerald-600">✓ LUNAS</span>
                                        @else
                                            <div class="space-y-1.5">
                                                @if($fine->payment_method === 'transfer' && $fine->payment_proof)
                                                    <form method="POST" action="{{ route('petugas.borrows.verify-transfer', $fine) }}">
                                                        @csrf
                                                        <input type="hidden" name="action" value="approve">
                                                        <button type="submit" class="w-full py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[10px] font-black uppercase tracking-wider shadow-2xs">Setujui Transfer</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('petugas.borrows.verify-transfer', $fine) }}">
                                                        @csrf
                                                        <input type="hidden" name="action" value="reject">
                                                        <button type="submit" class="w-full py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[9px] font-bold uppercase">Tolak</button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('petugas.borrows.cash-pay', $fine) }}">
                                                        @csrf
                                                        <button type="submit" class="w-full py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[10px] font-black uppercase tracking-wider shadow-2xs">Terima Cash</button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-400 font-bold text-xs">
                                        Tidak ada denda yang perlu ditindaklanjuti.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $fines->links() }}</div>
            </div>

            <!-- TAB 2: HUKUMAN SOSIAL -->
            <div x-show="tab === 'social'" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 overflow-hidden space-y-4" style="display: none;">
                <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                    <div>
                        <h3 class="font-black text-gray-900 text-base">Manajemen Hukuman Sosial Siswa</h3>
                        <p class="text-xs text-gray-500 font-medium">Beri tugas sosial pengganti denda (seperti membersihkan toilet atau menata rak buku) dan verifikasi penyelesaiannya.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="py-3 px-4 font-black text-gray-400 uppercase tracking-widest">Siswa</th>
                                <th class="py-3 px-4 font-black text-gray-400 uppercase tracking-widest">Buku</th>
                                <th class="py-3 px-4 font-black text-gray-400 uppercase tracking-widest">Tugas Sosial Diberikan</th>
                                <th class="py-3 px-4 font-black text-gray-400 uppercase tracking-widest">Status</th>
                                <th class="py-3 px-4 font-black text-gray-400 uppercase tracking-widest text-center w-40">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($socialPunishments as $social)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <!-- Siswa -->
                                    <td class="py-4 px-4 font-bold text-gray-900">
                                        {{ $social->user->name }}
                                        <span class="block text-[10px] font-normal text-gray-400">{{ $social->user->kelas ?? '-' }} ({{ $social->user->jurusan ?? '-' }})</span>
                                    </td>

                                    <!-- Buku -->
                                    <td class="py-4 px-4">
                                        <span class="font-bold text-gray-800 block truncate max-w-[180px]">{{ $social->book->title }}</span>
                                        <span class="font-mono text-[10px] text-gray-400">{{ $social->book->code }}</span>
                                    </td>

                                    <!-- Deskripsi Tugas -->
                                    <td class="py-4 px-4">
                                        <form method="POST" action="{{ route('petugas.borrows.assign-social', $social) }}" class="flex gap-2">
                                            @csrf
                                            <input type="text" name="social_punishment_description" value="{{ $social->social_punishment_description }}" placeholder="Ketik tugas (misal: Membersihkan toilet)" class="text-xs rounded-xl border-gray-200 focus:ring-amber-500 focus:border-amber-500 flex-1">
                                            <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-black text-white rounded-xl text-[10px] font-bold">Simpan</button>
                                        </form>
                                    </td>

                                    <!-- Status -->
                                    <td class="py-4 px-4">
                                        @if($social->social_punishment_status === 'completed')
                                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full text-[9px] font-black uppercase">SELESAI</span>
                                            <span class="block text-[8px] text-gray-400 mt-0.5">{{ optional($social->social_punishment_completed_at)->format('d M Y, H:i') }}</span>
                                        @else
                                            <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full text-[9px] font-black uppercase animate-pulse">DALAM TUGAS</span>
                                        @endif
                                    </td>

                                    <!-- Aksi -->
                                    <td class="py-4 px-4 text-center">
                                        @if($social->social_punishment_status !== 'completed')
                                            <form method="POST" action="{{ route('petugas.borrows.complete-social', $social) }}">
                                                @csrf
                                                <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[10px] font-black uppercase tracking-wider shadow-sm transition-all">
                                                    ✓ Tandai Selesai
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs font-bold text-gray-400">Tuntas</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-gray-400 font-bold text-xs">
                                        Belum ada data hukuman sosial.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
