<x-member-layout>
    <div x-data="loansApp()" x-init="init()" class="px-6 py-6 space-y-8 pb-32">
        <!-- Header Section -->
        <section>
            <h2 class="text-3xl font-bold text-gray-900 leading-[1.1]">My Loans</h2>
            <p class="text-gray-500 text-sm mt-3 leading-relaxed">Kelola bacaan aktif, penalti keterlambatan, dan riwayat peminjamanmu.</p>
        </section>

        @if (session('success'))
            <div class="px-4 py-3 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl text-xs font-bold animate-fade-in flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Seksi Booking & Antrean Aktif -->
        @if(isset($myQueues) && $myQueues->count() > 0)
            <section class="space-y-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <span>⏳</span> Booking & Antrean Saya
                    </h3>
                    <span class="px-3 py-1 bg-amber-50 text-amber-700 rounded-full text-[10px] font-bold uppercase tracking-wider">
                        {{ $myQueues->count() }} / 2 Buku
                    </span>
                </div>

                <div class="space-y-3">
                    @foreach($myQueues as $queue)
                        @php
                            $isReady = in_array($queue->status, [\App\Models\BookQueue::STATUS_READY, \App\Models\BookQueue::STATUS_CALLED]);
                            $position = $queue->getQueuePosition();
                            $estimatedDate = $queue->getEstimatedAvailableDate();
                        @endphp
                        <div class="bg-white rounded-3xl p-5 border {{ $isReady ? 'border-emerald-300 bg-emerald-50/20 ring-2 ring-emerald-100' : 'border-gray-100' }} shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-4 min-w-0">
                                <div class="w-16 h-22 rounded-2xl overflow-hidden bg-slate-50 shrink-0 border border-gray-100 shadow-2xs">
                                    <img src="{{ $queue->book->cover_image ? route('books.cover', $queue->book) : 'https://ui-avatars.com/api/?name=' . urlencode($queue->book->title) . '&background=f3f4f6&color=94a3b8' }}" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0 space-y-1">
                                    <div class="flex items-center gap-2">
                                        @if($isReady)
                                            <span class="px-2.5 py-0.5 bg-emerald-600 text-white rounded-lg text-[9px] font-black uppercase tracking-wider animate-pulse">
                                                ✓ SIAP DIAMBIL DI PERPUS
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-700 rounded-lg text-[9px] font-black uppercase tracking-wider border border-indigo-100">
                                                Antrean ke-{{ $position }}
                                            </span>
                                        @endif
                                    </div>
                                    <h4 class="font-bold text-gray-900 text-sm truncate max-w-sm">{{ $queue->book->title }}</h4>
                                    <p class="text-[11px] text-gray-500 font-medium">
                                        @if($isReady)
                                            Batas waktu ambil (2 hari): <strong class="text-emerald-700 font-black">{{ $queue->deadline ? $queue->deadline->format('d M Y, H:i') : '48 Jam' }}</strong>
                                        @else
                                            Estimasi buku siap: <strong class="text-indigo-700 font-black">~{{ $estimatedDate->format('d M Y') }}</strong>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <form method="POST" action="{{ route('member.queues.cancel', $queue) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan antrean buku ini?')">
                                    @csrf
                                    <button type="submit" class="px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-xl text-xs font-bold transition-all border border-rose-200 shadow-2xs active:scale-95 flex items-center gap-1.5">
                                        <span>✕</span>
                                        <span>Batalkan Booking</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Seksi Peminjaman Aktif & Terlambat -->
        <section class="space-y-4">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    Aktif & Berjalan
                </h3>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[10px] font-bold uppercase tracking-wider">
                    {{ $activeBorrows->count() }} Buku
                </span>
            </div>

            <div class="space-y-4">
                @forelse ($activeBorrows as $borrow)
                    @php
                        $now = now();
                        $isLate = strtolower($borrow->status) === 'late' || ($borrow->due_date && $now->gt($borrow->due_date));
                        $lateDays = $borrow->due_date && $now->gt($borrow->due_date)
                            ? max(0, \Carbon\Carbon::parse($borrow->due_date)->startOfDay()->diffInDays($now->startOfDay(), false))
                            : 0;
                        $fineAmount = $borrow->fine > 0 ? $borrow->fine : ($lateDays * 15000);
                    @endphp
                    <div class="bg-white rounded-3xl p-5 border {{ $isLate ? 'border-red-200 bg-red-50/10' : 'border-gray-100' }} shadow-sm flex flex-col gap-4 group transition-all">
                        <div class="flex gap-4 items-start">
                            <div class="w-20 h-28 rounded-2xl overflow-hidden bg-gray-50 shrink-0 shadow-inner border border-gray-100">
                                <img 
                                    src="{{ $borrow->book->cover_image ? route('books.cover', $borrow->book) : 'https://ui-avatars.com/api/?name=' . urlencode($borrow->book->title) . '&background=f3f4f6&color=94a3b8' }}" 
                                    class="w-full h-full object-cover" 
                                    alt="cover"
                                >
                            </div>
                            <div class="flex-1 min-w-0 flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-start gap-2">
                                        <h4 class="font-bold text-gray-900 leading-tight line-clamp-2">{{ $borrow->book->title }}</h4>
                                        @if($isLate)
                                            <span class="px-2.5 py-0.5 bg-red-600 text-white rounded-lg text-[9px] font-black uppercase tracking-widest shrink-0 animate-pulse">
                                                TERLAMBAT {{ $lateDays }} HARI
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Penulis: {{ $borrow->book->author ?: '-' }}</p>
                                </div>
                                
                                <div class="mt-3 flex flex-wrap items-center gap-3">
                                    <div class="bg-slate-50 rounded-xl px-3 py-1.5 flex items-center gap-2 border border-slate-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        <span class="text-[10px] font-bold text-gray-600">Tempo: <span class="{{ $isLate ? 'text-red-600' : 'text-gray-800' }}">{{ $borrow->due_date ? $borrow->due_date->format('d M Y') : '-' }}</span></span>
                                    </div>

                                    @if($isLate)
                                        <div class="bg-red-50 rounded-xl px-3 py-1.5 border border-red-100 flex items-center gap-1.5">
                                            <span class="text-[10px] font-black text-red-700">Denda: Rp {{ number_format($fineAmount, 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Panel Khusus Keterlambatan / Sanksi -->
                        @if($isLate)
                            <div class="pt-3 border-t border-red-100 space-y-3">
                                <!-- Status Sanksi Saat Ini -->
                                @if($borrow->punishment_type === 'social')
                                    <div class="p-4 bg-amber-50 rounded-2xl border border-amber-200 space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] font-black text-amber-800 uppercase tracking-wider flex items-center gap-1.5">
                                                <span>🧹</span> Hukuman Sosial
                                            </span>
                                            <span class="px-2 py-0.5 bg-amber-200 text-amber-900 rounded-full text-[8px] font-black uppercase">
                                                {{ $borrow->social_punishment_status === 'completed' ? 'SELESAI' : 'DALAM PELAKSANAAN' }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-amber-900 font-bold">
                                            Tugas: <span class="font-normal italic">"{{ $borrow->social_punishment_description ?: 'Silakan temui pustakawan untuk penentuan tugas sosial.' }}"</span>
                                        </p>
                                        @if($borrow->social_punishment_status !== 'completed')
                                            <p class="text-[9px] text-amber-700">Wajib temui pustakawan yang bertugas untuk verifikasi tugas setelah dikerjakan.</p>
                                        @endif
                                    </div>
                                @elseif($borrow->payment_status === 'pending_verification')
                                    <div class="p-3.5 bg-sky-50 rounded-2xl border border-sky-200 flex items-center justify-between">
                                        <div>
                                            <span class="text-[10px] font-black text-sky-800 uppercase tracking-wider block">Bukti Transfer Terkirim</span>
                                            <p class="text-xs text-sky-700 font-medium">Sedang diverifikasi oleh pustakawan/admin.</p>
                                        </div>
                                        <span class="px-2.5 py-1 bg-sky-200 text-sky-900 rounded-full text-[8px] font-black uppercase">Pending</span>
                                    </div>
                                @elseif($borrow->payment_status === 'paid' || $borrow->fine_paid_at)
                                    <div class="p-3.5 bg-emerald-50 rounded-2xl border border-emerald-200 flex items-center justify-between">
                                        <span class="text-[10px] font-black text-emerald-800 uppercase tracking-wider flex items-center gap-1.5">
                                            <span>✓</span> Denda Telah Lunas
                                        </span>
                                        <span class="px-2.5 py-1 bg-emerald-200 text-emerald-900 rounded-full text-[8px] font-black uppercase">Lunas</span>
                                    </div>
                                @endif

                                <!-- Tombol Aksi Pilih / Atur Sanksi Keterlambatan -->
                                <button 
                                    type="button"
                                    @click="openPunishmentModal({{ $borrow->id }}, '{{ $borrow->book->title }}', {{ $lateDays }}, {{ $fineAmount }}, '{{ $borrow->punishment_type }}', '{{ $borrow->payment_method }}', '{{ addslashes($borrow->late_reason) }}')"
                                    class="w-full py-3 bg-red-600 hover:bg-red-700 text-white rounded-2xl text-xs font-black uppercase tracking-wider shadow-sm transition-all flex items-center justify-center gap-2 active:scale-95"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                                    <span>Pilih Sanksi / Bayar Denda / Upload Bukti Sakit</span>
                                </button>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-8 text-center bg-gray-50 rounded-3xl border border-dashed border-gray-200">
                        <p class="text-gray-400 text-sm">Tidak ada pinjaman aktif.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Seksi Upload Rangkuman & Penalti Rangkuman -->
        <section class="bg-indigo-50/50 rounded-[2.5rem] p-6 sm:p-8 border border-indigo-100/30">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 bg-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-book-check"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M18 6h2"/><path d="M18 10h2"/><path d="M18 14h2"/><path d="M8 6h3"/><path d="M8 10h3"/><path d="M8 14h3"/><path d="m9 17 2 2 4-4"/></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Upload Rangkuman</h3>
                    <p class="text-xs text-gray-500 font-medium">Kirim rangkuman bacaanmu. Terlambat mengumpulkan dikenakan penalti tambahan 1 lembar per hari.</p>
                </div>
            </div>

            <div class="space-y-4">
                @foreach($activeBorrows as $borrow)
                    @php
                        $now = now();
                        $targetDate = $borrow->due_date ?? $borrow->borrow_date?->copy()->addDays(7);
                        $summaryLateDays = $targetDate && $now->gt($targetDate)
                            ? max(0, \Carbon\Carbon::parse($targetDate)->startOfDay()->diffInDays($now->startOfDay(), false))
                            : 0;
                        $extraPages = $summaryLateDays * 1;
                        $status = $borrow->summary?->status;
                        $summaryText = $status === 'approved' ? 'Approved' : ($status === 'rejected' ? 'Rejected / Revision' : ($status === 'pending' ? 'Pending Review' : 'Belum Upload'));
                        $statusClass = $status === 'approved' ? 'text-emerald-600' : ($status === 'rejected' ? 'text-red-600' : 'text-amber-600');
                    @endphp
                    <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-sm space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-400 font-bold uppercase truncate max-w-[200px]">{{ $borrow->book->title }}</p>
                                <p class="text-sm font-black mt-0.5 {{ $statusClass }}">{{ $summaryText }}</p>
                            </div>
                            
                            @if($status === 'approved')
                                <div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                </div>
                            @elseif($status === 'pending')
                                <div class="w-8 h-8 bg-amber-500 rounded-full flex items-center justify-center text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4"/><path d="M12 18v4"/><path d="M4.93 4.93l2.83 2.83"/><path d="M16.24 16.24l2.83 2.83"/><path d="M2 12h4"/><path d="M18 12h4"/></svg>
                                </div>
                            @endif
                        </div>

                        <!-- Peringatan Penalti Lembar jika Terlambat -->
                        @if($summaryLateDays > 0 && (!$status || $status === 'rejected'))
                            <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-2xl flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center shrink-0 font-black text-sm">!</div>
                                <div class="text-xs">
                                    <p class="font-black text-rose-800 uppercase tracking-wider">Keterlambatan {{ $summaryLateDays }} Hari</p>
                                    <p class="text-rose-700 font-medium mt-0.5">Wajib menambah <span class="font-black underline">+{{ $extraPages }} Lembar Rangkuman</span>.</p>
                                </div>
                            </div>
                        @endif

                        <!-- Form Upload Rangkuman Lengkap dengan Alasan & Bukti Dokter -->
                        @if(!$status || $status === 'rejected')
                            <form method="POST" action="{{ route('member.summary.store', $borrow) }}" enctype="multipart/form-data" class="space-y-3 pt-2 border-t border-slate-100">
                                @csrf
                                
                                <div>
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1">File Rangkuman (PDF / Foto JPG/PNG)</label>
                                    <input type="file" name="file" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer" required>
                                </div>

                                @if($summaryLateDays > 0)
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1">Alasan Keterlambatan</label>
                                            <input type="text" name="late_reason" placeholder="Contoh: Sakit demam berdarah / rawat inap" class="w-full text-xs rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1">Upload Bukti Sakit / Surat Dokter</label>
                                            <input type="file" name="late_evidence" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                                        </div>
                                    </div>
                                @endif

                                <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-sm transition-all active:scale-95 flex items-center justify-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                                    <span>Kirim Rangkuman {{ $summaryLateDays > 0 ? "(+{$extraPages} Lembar)" : '' }}</span>
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Seksi Riwayat Peminjaman (History) -->
        <section class="space-y-4">
            <h3 class="text-xl font-bold text-gray-900">Riwayat Pengembalian</h3>
            <div class="space-y-4">
                @forelse ($historyBorrows as $history)
                    @php
                        $summary = $history->summary;
                        $hasReview = $history->book->reviews->isNotEmpty();
                    @endphp
                    <div class="bg-white rounded-[2rem] p-5 border border-gray-100 shadow-sm flex flex-col gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-gray-50 rounded-2xl overflow-hidden shadow-sm shrink-0 flex items-center justify-center p-1">
                                <img src="{{ $history->book->cover_image ? route('books.cover', $history->book) : 'https://ui-avatars.com/api/?name=' . urlencode($history->book->title) . '&background=f3f4f6&color=94a3b8' }}" class="w-full h-full object-contain rounded-xl" alt="cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-bold text-gray-800 truncate">{{ $history->book->title }}</h4>
                                <p class="text-[10px] text-gray-400 mt-1">Dikembalikan {{ $history->return_date ? $history->return_date->format('d M Y') : '-' }}</p>
                            </div>
                        </div>

                        <!-- Status & Actions -->
                        <div class="pt-2 border-t border-dashed border-gray-100 flex flex-col gap-3">
                            <div class="flex items-center justify-between">
                                @if(auth()->user()->member_type !== 'teacher')
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Rangkuman:</span>
                                    @if(!$summary)
                                        <span class="text-[10px] font-bold text-amber-600 uppercase">Belum Upload</span>
                                    @elseif($summary->status === 'approved')
                                        <span class="text-[10px] font-bold text-emerald-600 uppercase flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>
                                            Approved
                                        </span>
                                    @elseif($summary->status === 'rejected')
                                        <span class="text-[10px] font-bold text-red-600 uppercase">Rejected</span>
                                    @else
                                        <span class="text-[10px] font-bold text-indigo-600 uppercase animate-pulse">Pending Review</span>
                                    @endif
                                </div>
                                @endif
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Rating:</span>
                                    @if($hasReview)
                                        <span class="text-[10px] font-bold text-emerald-600 uppercase flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                            Sudah Diisi
                                        </span>
                                    @else
                                        <span class="text-[10px] font-bold text-amber-600 uppercase">Belum Diisi</span>
                                    @endif
                                </div>
                            </div>

                            @if(!$hasReview)
                                <a 
                                    href="{{ route('member.books.detail', $history->book->id) }}"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-xl text-[10px] font-bold uppercase tracking-wider active:scale-95 transition-all"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    Beri Rating Buku
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center bg-gray-50/50 rounded-[2rem] border border-dashed border-gray-200">
                        <p class="text-gray-400 text-sm">Belum ada riwayat pengembalian.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- MODAL INTERAKTIF: PILIH SANKSI / BAYAR DENDA / UPLOAD BUKTI -->
        <div 
            x-show="isPunishmentModalOpen" 
            class="fixed inset-0 z-[70] flex items-center justify-center p-4 sm:p-6 overflow-y-auto"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none;"
        >
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="isPunishmentModalOpen = false"></div>
            
            <div 
                x-show="isPunishmentModalOpen"
                class="relative bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden border border-gray-100 p-6 sm:p-8 space-y-6 max-h-[90vh] overflow-y-auto"
            >
                <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                    <div>
                        <h3 class="text-lg font-black text-gray-900">Sanksi Keterlambatan</h3>
                        <p class="text-xs text-gray-500 font-medium" x-text="punishmentBookTitle"></p>
                    </div>
                    <button @click="isPunishmentModalOpen = false" class="text-gray-400 hover:text-gray-600 font-black text-lg">✕</button>
                </div>

                <form @submit.prevent="submitPunishmentForm($event)" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    
                    <!-- Pilihan 2 Jenis Hukuman -->
                    <div>
                        <input type="hidden" name="punishment_type" :value="selectedPunishmentType">
                        <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">Pilih Jenis Hukuman</label>
                        <div class="grid grid-cols-2 gap-3">
                            <button 
                                type="button"
                                @click="selectedPunishmentType = 'fine'"
                                class="p-4 rounded-2xl border-2 transition-all flex flex-col items-center text-center gap-1.5 active:scale-95 cursor-pointer"
                                :class="selectedPunishmentType === 'fine' ? 'border-emerald-600 bg-emerald-50/50 shadow-sm ring-2 ring-emerald-100' : 'border-gray-200 bg-white hover:border-gray-300'"
                            >
                                <span class="text-2xl">💰</span>
                                <span class="text-xs font-black text-gray-900">Hukuman Denda</span>
                                <span class="text-[10px] text-emerald-700 font-bold">Rp 15.000 / Hari</span>
                            </button>

                            <button 
                                type="button"
                                @click="selectedPunishmentType = 'social'"
                                class="p-4 rounded-2xl border-2 transition-all flex flex-col items-center text-center gap-1.5 active:scale-95 cursor-pointer"
                                :class="selectedPunishmentType === 'social' ? 'border-amber-600 bg-amber-50/50 shadow-sm ring-2 ring-amber-100' : 'border-gray-200 bg-white hover:border-gray-300'"
                            >
                                <span class="text-2xl">🧹</span>
                                <span class="text-xs font-black text-gray-900">Hukuman Sosial</span>
                                <span class="text-[10px] text-amber-700 font-bold">Tugas Kebersihan/Rak</span>
                            </button>
                        </div>
                    </div>

                    <!-- Panel Opsi Denda Finansial -->
                    <div x-show="selectedPunishmentType === 'fine'" class="space-y-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 animate-fade-in">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-200/60">
                            <span class="text-xs text-gray-500 font-medium">Total Denda (<span x-text="punishmentLateDays"></span> Hari):</span>
                            <span class="text-sm font-black text-red-600" x-text="formatRupiah(punishmentFineAmount)"></span>
                        </div>

                        <!-- Pilihan Metode Pembayaran: Cash / Transfer -->
                        <div>
                            <input type="hidden" name="payment_method" :value="selectedPaymentMethod">
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-2">Metode Pembayaran</label>
                            <div class="grid grid-cols-2 gap-3">
                                <button 
                                    type="button"
                                    @click="selectedPaymentMethod = 'cash'"
                                    class="p-3 rounded-xl border flex items-center justify-center gap-2 text-xs font-bold transition-all active:scale-95 cursor-pointer"
                                    :class="selectedPaymentMethod === 'cash' ? 'bg-white border-emerald-600 text-emerald-800 shadow-xs ring-1 ring-emerald-500' : 'bg-slate-100 border-transparent text-gray-600 hover:bg-slate-200'"
                                >
                                    <span>💵</span>
                                    <span>Tunai (Cash)</span>
                                </button>
                                <button 
                                    type="button"
                                    @click="selectedPaymentMethod = 'transfer'"
                                    class="p-3 rounded-xl border flex items-center justify-center gap-2 text-xs font-bold transition-all active:scale-95 cursor-pointer"
                                    :class="selectedPaymentMethod === 'transfer' ? 'bg-white border-emerald-600 text-emerald-800 shadow-xs ring-1 ring-emerald-500' : 'bg-slate-100 border-transparent text-gray-600 hover:bg-slate-200'"
                                >
                                    <span>💳</span>
                                    <span>Transfer Bank</span>
                                </button>
                            </div>
                        </div>

                        <!-- Info Jika Cash -->
                        <div x-show="selectedPaymentMethod === 'cash'" class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-900 space-y-1">
                            <p class="font-bold">⚠️ Pembayaran Tunai (Cash):</p>
                            <p class="text-[11px] leading-relaxed">Silakan langsung menemui <strong>Pustakawan yang sedang bertugas</strong> di meja sirkulasi untuk menyerahkan uang denda.</p>
                        </div>

                        <!-- Info & Upload Bukti Jika Transfer -->
                        <div x-show="selectedPaymentMethod === 'transfer'" class="space-y-3">
                            <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-200 text-xs space-y-2 text-emerald-900">
                                <p class="font-black text-[10px] uppercase tracking-wider text-emerald-800">Rekening Resmi Pembayaran Denda:</p>
                                <div class="bg-white p-3 rounded-lg border border-emerald-100 font-mono text-xs space-y-1">
                                    <p><strong>Bank BCA:</strong> 8820-1928-3847-1029</p>
                                    <p><strong>Bank BRI:</strong> 0192-0100-2938-531</p>
                                    <p><strong>Dana / GoPay:</strong> 0812-3456-7890</p>
                                    <p class="text-[10px] text-gray-500 font-sans mt-1">a/n <strong>Perpustakaan Skarifta</strong></p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1">Upload Bukti Transfer (Struk / Foto Bukti Bayar)</label>
                                <input type="file" name="payment_proof" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-emerald-100 file:text-emerald-800 hover:file:bg-emerald-200 cursor-pointer">
                            </div>
                        </div>
                    </div>

                    <!-- Panel Opsi Hukuman Sosial -->
                    <div x-show="selectedPunishmentType === 'social'" class="p-4 bg-amber-50 rounded-2xl border border-amber-200 text-xs text-amber-900 space-y-2">
                        <p class="font-black text-[11px] uppercase tracking-wider">⚠️ Prosedur Hukuman Sosial:</p>
                        <p class="text-xs leading-relaxed">
                            Anda wajib <strong>menemui pustakawan yang sedang bertugas</strong>. Pustakawan akan menentukan tugas sosial (seperti membersihkan toilet atau menata rak buku) dan mencatatnya ke dalam sistem.
                        </p>
                    </div>

                    <!-- Kolom Alasan Keterlambatan & Upload Bukti Surat Dokter -->
                    <div class="space-y-3 pt-3 border-t border-gray-100">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Keterangan Alasan (Opsional / Keringanan)</span>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Alasan Keterlambatan</label>
                            <textarea name="late_reason" x-model="punishmentReason" rows="2" placeholder="Tulis alasan kenapa terlambat mengembalikan buku (misal: sakit / rawat inap)..." class="w-full text-xs rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Upload Surat Dokter / Bukti Pendukung</label>
                            <input type="file" name="late_evidence" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                            <p class="text-[9px] text-gray-400 mt-1">Sertakan foto surat dokter jika terlambat karena sakit untuk pertimbangan pustakawan.</p>
                        </div>
                    </div>

                    <div class="pt-3">
                        <button type="submit" class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-black uppercase tracking-wider shadow-lg shadow-emerald-200 transition-all active:scale-95">
                            Simpan & Kirim Konfirmasi Sanksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function loansApp() {
            return {
                isPunishmentModalOpen: false,
                punishmentBorrowId: null,
                punishmentBookTitle: '',
                punishmentLateDays: 0,
                punishmentFineAmount: 0,
                selectedPunishmentType: 'fine',
                selectedPaymentMethod: 'cash',
                punishmentReason: '',

                init() {
                    console.log("Loans App Ready");
                },

                openPunishmentModal(borrowId, title, lateDays, fineAmount, punishmentType, paymentMethod, reason) {
                    this.punishmentBorrowId = borrowId;
                    this.punishmentBookTitle = title;
                    this.punishmentLateDays = lateDays;
                    this.punishmentFineAmount = fineAmount;
                    this.selectedPunishmentType = punishmentType || 'fine';
                    this.selectedPaymentMethod = paymentMethod || 'cash';
                    this.punishmentReason = reason || '';
                    this.isPunishmentModalOpen = true;
                },

                formatRupiah(num) {
                    return 'Rp ' + (new Intl.NumberFormat('id-ID').format(num || 0));
                },

                submitPunishmentForm(e) {
                    const form = e.target;
                    form.action = `{{ url('/member/borrows') }}/${this.punishmentBorrowId}/punishment`;
                    form.submit();
                }
            }
        }
    </script>
</x-member-layout>
