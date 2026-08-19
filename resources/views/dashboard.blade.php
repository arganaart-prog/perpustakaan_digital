<x-member-layout>
    <div x-data="dashboardApp()" x-init="init()" class="px-6 py-6 space-y-8 pb-24">
        <!-- Hero / Greeting -->
        <section class="relative bg-emerald-600 rounded-[2.5rem] p-8 overflow-hidden shadow-xl shadow-emerald-100">
            <div class="relative z-10">
                <p class="text-emerald-100 text-[10px] font-black uppercase tracking-[0.3em] mb-2 opacity-80">Premium Library Access</p>
                <h2 class="text-2xl font-black text-white leading-tight">
                    Halo, {{ explode(' ', Auth::user()->name)[0] }}! 👋
                </h2>
                <p class="text-emerald-50/80 text-[11px] font-medium mt-3 leading-relaxed max-w-[200px]">
                    Siap untuk menjelajah cakrawala ilmu baru hari ini?
                </p>
                
                <div class="mt-8 flex gap-3">
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl px-5 py-3 border border-white/10 shrink-0">
                        <p class="text-[9px] text-emerald-100 font-black uppercase tracking-widest opacity-60">Pinjaman</p>
                        <p class="text-xl font-black text-white leading-none mt-1">{{ $activeLoansCount }}</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl px-5 py-3 border border-white/10 shrink-0">
                        <p class="text-[9px] text-emerald-100 font-black uppercase tracking-widest opacity-60">Ready</p>
                        <p class="text-xl font-black text-white leading-none mt-1">{{ $readyQueuesCount }}</p>
                    </div>
                    @if($unpaidFinesCount > 0)
                        <div class="bg-red-500/20 backdrop-blur-md rounded-2xl px-5 py-3 border border-red-400/20 shrink-0 animate-pulse">
                            <p class="text-[9px] text-red-100 font-black uppercase tracking-widest opacity-80">Denda</p>
                            <p class="text-xl font-black text-white leading-none mt-1">Rp {{ number_format($totalUnpaidFineAmount, 0, ',', '.') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Summary Action Button -->
            <button 
                @click="openSummaryModal()"
                class="absolute bottom-8 right-8 z-20 w-14 h-14 bg-white text-emerald-600 rounded-full shadow-2xl flex items-center justify-center active:scale-90 transition-transform hover:bg-emerald-50"
                title="Kumpulkan Rangkuman"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
            </button>

            <!-- Absolute Decorations -->
            <div class="absolute -top-10 -right-10 w-48 h-48 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-6 right-10 opacity-10 transform rotate-12 scale-150">
                <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-library"><path d="m16 6 4 14"/><path d="M12 6v14"/><path d="M8 8v12"/><path d="M4 4v16"/></svg>
            </div>
        </section>

        <!-- Trending Section (Global) -->
        <section>
            <div class="flex items-end justify-between mb-5 px-1">
                <div>
                    <h3 class="text-lg font-black text-gray-900 tracking-tight">Trending Now</h3>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">Paling Banyak Dibaca</p>
                </div>
                <a href="{{ route('member.books.index') }}" class="text-emerald-600 font-black text-[10px] uppercase tracking-widest hover:underline px-2 py-1">Lihat Semua</a>
            </div>
            
            <div class="flex gap-5 overflow-x-auto pb-6 -mx-6 px-6 no-scrollbar">
                @foreach($trendingBooks as $book)
                    <a href="{{ route('member.books.detail', $book) }}" class="w-32 shrink-0 group cursor-pointer block">
                        <div class="relative aspect-[3/4] rounded-[1.8rem] overflow-hidden bg-gray-50 shadow-sm border border-gray-100/50 mb-3 active:scale-95 transition-all duration-300">
                            <img 
                                src="{{ $book->cover_image ? route('books.cover', $book) : 'https://ui-avatars.com/api/?name=' . urlencode($book->title) . '&background=f3f4f6&color=94a3b8&size=512' }}" 
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" 
                                alt="cover"
                            >
                            <div class="absolute top-2.5 left-2.5 w-6 h-6 bg-white/90 backdrop-blur-md rounded-full flex items-center justify-center shadow-sm">
                                <span class="text-[10px] font-black text-gray-900">{{ $loop->iteration }}</span>
                            </div>
                        </div>
                        <h4 class="text-[11px] font-black text-gray-900 line-clamp-1 leading-tight px-1 uppercase tracking-tight">{{ $book->title }}</h4>
                        <p class="text-[9px] text-gray-400 font-bold px-1 mt-1 truncate tracking-wide uppercase">{{ $book->author ?: 'Unknown' }}</p>
                    </a>
                @endforeach
            </div>
        </section>

        <!-- New Arrivals & Quick Links -->
        <section>
            <div class="mb-5 px-1">
                <h3 class="text-lg font-black text-gray-900 tracking-tight">New Arrivals</h3>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">Koleksi Terbaru</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($newArrivals as $book)
                    <a href="{{ route('member.books.detail', $book) }}" class="bg-white rounded-[2rem] p-4 border border-gray-100 shadow-sm flex gap-4 active:scale-[0.98] transition-all cursor-pointer group hover:border-emerald-100">
                        <div class="w-16 h-20 rounded-2xl overflow-hidden bg-gray-50 shrink-0 shadow-inner">
                             <img 
                                src="{{ $book->cover_image ? route('books.cover', $book) : 'https://ui-avatars.com/api/?name=' . urlencode($book->title) . '&background=f3f4f6&color=94a3b8' }}" 
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                                alt="cover"
                            >
                        </div>
                        <div class="min-w-0 flex flex-col justify-center">
                            <h4 class="text-[12px] font-black text-gray-900 leading-tight line-clamp-1 mb-1 uppercase tracking-tight">{{ $book->title }}</h4>
                            <p class="text-[10px] text-gray-400 font-bold truncate tracking-wide uppercase">{{ $book->author ?: '-' }}</p>
                            <div class="mt-2 text-[8px] font-black text-emerald-600 uppercase tracking-widest">
                                {{ $book->category ?: 'General' }}
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        <!-- Fines Section (Only if exists) -->
        @if($activeFines->isNotEmpty())
        <section class="animate-fade-in group">
            <div class="mb-5 px-1 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-gray-900 tracking-tight">Tagihan & Denda</h3>
                    <p class="text-[10px] text-red-500 font-bold uppercase tracking-widest mt-0.5 flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-alert-triangle animate-pulse"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                        Selesaikan administrasi denda Anda
                    </p>
                </div>
            </div>
            
            <div class="bg-white rounded-[2.5rem] border border-red-50 shadow-sm overflow-hidden mb-8">
                <div class="p-6 space-y-4">
                    @foreach($activeFines as $fine)
                        <div class="flex items-center justify-between gap-4 p-4 bg-red-50/30 rounded-3xl border border-red-50/50">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 15h2a2 2 0 1 0 0-4h-3c-.6 0-1.1-.2-1.4-.6L7 8.3"/><path d="m7 15 3-3"/><path d="m14 9 3-3"/><path d="M9 22h6"/><path d="M12 2v2"/><path d="M12 18v2"/></svg>
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-gray-900 line-clamp-1 leading-tight mb-1 uppercase tracking-tight">{{ $fine['book_title'] }}</p>
                                    <p class="text-[9px] text-red-600 font-bold uppercase tracking-widest">{{ $fine['reason'] }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-black text-gray-900">Rp {{ number_format($fine['amount'], 0, ',', '.') }}</p>
                                <p class="text-[8px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">Harus Dibayar</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="px-6 py-4 bg-red-50/50 border-t border-red-50 flex items-center justify-between gap-4 text-center sm:text-left">
                    <p class="text-[9px] font-bold text-red-700/60 leading-tight uppercase tracking-wider">Bayar denda di petugas perpustakaan untuk melanjutkan peminjaman.</p>
                </div>
            </div>
        </section>
        @endif

        <!-- Truly Elegant Premium Book Detail Modal -->
        <div 
            x-show="isBookModalOpen" 
            class="fixed inset-0 z-50 flex items-start justify-center p-6 overflow-y-auto"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none;"
        >
            <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-xl" @click="closeBook()"></div>
            <div 
                x-show="isBookModalOpen"
                class="relative bg-white/95 w-full max-w-sm rounded-[2.5rem] shadow-[0_32px_64px_-16px_rgba(0,0,0,0.25)] overflow-hidden border border-white/20 my-8"
                x-transition:enter="transition-spring duration-500"
                x-transition:enter-start="opacity-0 scale-90 translate-y-8"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            >
                <!-- Decorative Top Graphic -->
                <div class="h-32 bg-gradient-to-br from-emerald-50 to-white relative overflow-hidden">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-emerald-100/40 via-transparent to-transparent"></div>
                    <button @click="closeBook()" class="absolute top-6 right-6 z-10 p-2 text-gray-400 hover:text-gray-900 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>

                <!-- Floating Cover Area -->
                <div class="px-8 -mt-20 relative z-10 flex flex-col items-center text-center">
                    <div class="w-32 aspect-[3/4] bg-white rounded-2xl shadow-[0_20px_40px_-10px_rgba(0,0,0,0.3)] border-[6px] border-white overflow-hidden transform transition-transform duration-500 hover:scale-105">
                        <template x-if="!selectedBook">
                            <div class="w-full h-full bg-gray-100 animate-pulse flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#e5e7eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                            </div>
                        </template>
                        <template x-if="selectedBook">
                            <img :src="selectedBook?.cover_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(selectedBook?.title || 'Book')}&background=f0fdf4&color=15803d&size=512`" 
                                 class="w-full h-full object-cover">
                        </template>
                    </div>
                    <div class="text-center">
                        <span :class="getStatusClass(selectedBook?.status)" class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-[0.1em] shadow-sm mb-4 inline-block" x-text="getStatusText(selectedBook?.status)"></span>
                        <h2 class="text-xl font-black text-gray-900 leading-tight mb-1" x-text="selectedBook?.title"></h2>
                        <p class="text-xs font-bold text-emerald-600/80 uppercase tracking-widest mb-4" x-text="selectedBook?.author"></p>
                        
                        <!-- Statistik Buku -->
                        <div class="flex items-center justify-center gap-4 py-3 border-y border-gray-100/50">
                            <div class="flex flex-col items-center gap-1">
                                <div class="flex items-center gap-1">
                                    <template x-for="i in 5">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" :fill="(selectedBook?.avg_rating || 0) >= i ? '#FBBF24' : 'none'" :stroke="(selectedBook?.avg_rating || 0) >= i ? '#FBBF24' : '#E5E7EB'" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    </template>
                                    <span class="text-xs font-black text-gray-700 ml-1" x-text="(selectedBook?.avg_rating ? Number(selectedBook.avg_rating).toFixed(1) : '0.0')"></span>
                                </div>
                                <p class="text-[8px] font-bold text-gray-300 uppercase tracking-widest">Average Rating</p>
                            </div>
                            <div class="w-px h-8 bg-gray-100"></div>
                            <div class="flex flex-col items-center gap-1">
                                <div class="flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-500"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M8 7h6"/><path d="M8 11h6"/><path d="M8 15h6"/></svg>
                                    <span class="text-[10px] font-black text-gray-700" x-text="selectedBook?.borrows_count || 0"></span>
                                </div>
                                <p class="text-[8px] font-bold text-gray-300 uppercase tracking-widest">Total Pinjam</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Body -->
                <div class="p-8 pt-0 space-y-6">
                    <!-- Queue Status Card (NEW) -->
                    <template x-if="selectedBook">
                        <div x-show="(selectedBook?.active_queues_count || 0) > 0 || (selectedBook?.status === 'borrowed' || selectedBook?.status === 'reserved')" class="bg-indigo-50/50 rounded-3xl p-5 border border-indigo-100/50 -mt-2">
                             <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-200 shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <template x-if="selectedBook.user_queue_position">
                                        <div>
                                            <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-0.5">Posisi Antrean Anda</p>
                                            <p class="text-[13px] font-black text-gray-900 leading-none">Peringkat <span class="text-indigo-600" x-text="selectedBook.user_queue_position"></span> dari <span x-text="selectedBook.active_queues_count"></span> Orang</p>
                                        </div>
                                    </template>
                                    <template x-if="!selectedBook.user_queue_position">
                                        <div>
                                            <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-0.5">Antrean Saat Ini</p>
                                            <p class="text-[13px] font-black text-gray-900 leading-none"><span x-text="selectedBook.active_queues_count"></span> Member Sedang Menunggu</p>
                                        </div>
                                    </template>
                                </div>
                             </div>
                        </div>
                    </template>
                     <!-- Info Grid -->
                    <div class="grid grid-cols-2 gap-x-6 gap-y-4 mb-8">
                        <div class="flex flex-col">
                            <p class="text-[8px] font-black text-gray-300 uppercase tracking-widest mb-1 tracking-[0.2em]">Kategori</p>
                            <p class="text-[11px] font-bold text-gray-700 font-black" x-text="selectedBook?.category || '-'"></p>
                        </div>
                        <div class="flex flex-col">
                            <p class="text-[8px] font-black text-gray-300 uppercase tracking-widest mb-1 tracking-[0.2em]">Rak / Lokasi</p>
                            <p class="text-[11px] font-bold text-gray-700 font-black" x-text="selectedBook?.rack_code || '-'"></p>
                        </div>
                        <div class="flex flex-col">
                            <p class="text-[8px] font-black text-gray-300 uppercase tracking-widest mb-1 tracking-[0.2em]">Halaman</p>
                            <p class="text-[11px] font-bold text-gray-700 font-black" x-text="`${selectedBook?.pages || '0'} Hal`"></p>
                        </div>
                        <div class="flex flex-col">
                            <p class="text-[8px] font-black text-gray-300 uppercase tracking-widest mb-1 tracking-[0.2em]">Ketersediaan</p>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] font-black" :class="selectedBook?.stock_available > 0 ? 'text-emerald-600' : 'text-red-500'" x-text="`${selectedBook?.stock_available || 0} Tersedia`"></span>
                                <span class="text-[10px] text-gray-300">/</span>
                                <span class="text-[10px] font-bold text-gray-500" x-text="`${selectedBook?.stock_total || 0} Total`"></span>
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <p class="text-[8px] font-black text-gray-300 uppercase tracking-widest mb-1 tracking-[0.2em]">Warna Label</p>
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full shadow-inner" :style="`background-color: ${selectedBook?.label_color || '#e5e7eb'}`"></div>
                                <p class="text-[11px] font-bold text-gray-700 font-black" x-text="selectedBook?.label_color || '-'"></p>
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <p class="text-[8px] font-black text-gray-300 uppercase tracking-widest mb-1 tracking-[0.2em]">Terdaftar Sejak</p>
                            <p class="text-[11px] font-bold text-gray-700 font-black" x-text="selectedBook?.added_at || '-'"></p>
                        </div>
                    </div>

                    <!-- Borrow History Section (Anonymized for Privacy) -->
                    <div class="space-y-4 py-6 border-y border-gray-100/50" x-show="selectedBook?.borrow_history?.length">
                        <div class="flex items-center justify-between">
                            <h4 class="text-[10px] font-black text-gray-900 uppercase tracking-widest text-emerald-600">Jejak Pembaca</h4>
                            <span class="text-[8px] font-bold text-gray-300 uppercase tracking-widest">Aktivitas Terkini</span>
                        </div>
                        <div class="space-y-3">
                            <template x-for="(hist, index) in selectedBook?.borrow_history || []" :key="index">
                                <div class="flex items-center justify-between p-3 bg-gray-50/10 rounded-2xl border border-gray-100/30">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 border border-emerald-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-[9px] font-black text-gray-600 italic" x-text="hist.user_name"></p>
                                            <p class="text-[8px] font-bold text-gray-400 uppercase tracking-tight" x-text="hist.borrow_date"></p>
                                        </div>
                                    </div>
                                    <div class="text-[8px] font-black text-gray-300 uppercase tracking-widest" x-text="hist.return_date"></div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Rating Form (If Eligible) -->
                    <div x-show="selectedBook?.can_review" class="bg-gray-50 rounded-[2rem] p-6 space-y-4 border border-emerald-100/50">
                        <div class="text-center">
                            <h4 class="text-[10px] font-black text-gray-900 uppercase tracking-widest">Beri Rating Kamu</h4>
                            <p class="text-[8px] text-gray-400 font-bold uppercase tracking-widest mt-1">Bagikan kesanmu tentang buku ini</p>
                        </div>
                        <div class="flex justify-center gap-2">
                            <template x-for="i in 5">
                                <button @click="rating = i" class="transition-transform active:scale-90 duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" :fill="rating >= i ? '#FBBF24' : 'none'" :stroke="rating >= i ? '#FBBF24' : '#E5E7EB'" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                </button>
                            </template>
                        </div>
                        <textarea 
                            x-model="comment"
                            rows="3" 
                            placeholder="Tulis ulasan singkat..."
                            class="w-full rounded-[1.5rem] border-none bg-white focus:ring-2 focus:ring-emerald-500/20 transition-all text-sm py-4 px-5 shadow-sm"
                        ></textarea>
                        <button 
                            @click="submitDashboardReview()" 
                            :disabled="rating === 0 || !comment"
                            class="w-full py-4 bg-emerald-600 text-white text-[10px] font-black rounded-2xl shadow-xl shadow-emerald-200 active:scale-95 transition-all uppercase tracking-widest disabled:opacity-50"
                        >
                            Kirim Ulasan
                        </button>
                    </div>

                    <!-- Reader Reviews Section -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-[10px] font-black text-gray-900 uppercase tracking-widest">Ulasan Pembaca</h4>
                            <span class="text-[9px] font-bold text-gray-400" x-text="`${selectedBook?.reviews_count || 0} ulasan`"></span>
                        </div>
                        
                        <div class="space-y-3 max-h-48 overflow-y-auto pr-2 no-scrollbar">
                            <template x-for="rev in selectedBook?.reviews || []" :key="rev.id">
                                <div class="p-4 bg-gray-50/50 rounded-2xl border border-gray-50">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <p class="text-[10px] font-black text-gray-900" x-text="rev.user_name"></p>
                                            <span x-show="rev.status === 'pending'" class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[8px] font-black rounded-lg uppercase">Moderasi</span>
                                        </div>
                                        <div class="flex text-amber-400">
                                            <template x-for="i in 5">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="8" height="8" viewBox="0 0 24 24" :fill="rev.rating >= i ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                            </template>
                                        </div>
                                    </div>
                                    <p class="text-[11px] text-gray-500 leading-relaxed font-medium" x-text="rev.comment"></p>
                                </div>
                            </template>
                            <div x-show="!selectedBook?.reviews?.length" class="py-6 text-center">
                                <p class="text-[10px] font-bold text-gray-300 uppercase tracking-widest">Belum ada ulasan</p>
                            </div>
                        </div>
                    </div>

                    <button @click="closeBook()" class="w-full py-4.5 bg-gray-900 text-white text-[10px] font-black rounded-2xl shadow-2xl active:scale-[0.98] transition-all uppercase tracking-[0.2em] relative overflow-hidden group">
                        <span class="relative z-10">Tutup Detail</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-emerald-600 to-teal-600 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Success Modal (Premium Look) -->
        <div 
            x-show="isSuccessModalOpen" 
            class="fixed inset-0 z-[100] flex items-start justify-center p-6 overflow-y-auto"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none;"
        >
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-md"></div>
            <div 
                x-show="isSuccessModalOpen"
                class="relative bg-white w-full max-w-xs rounded-[3rem] shadow-2xl p-10 text-center overflow-hidden"
                x-transition:enter="transition-spring duration-700"
                x-transition:enter-start="opacity-0 scale-50 translate-y-24"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            >
                <!-- Confetti/Sparkle decoration -->
                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-emerald-400 via-teal-500 to-emerald-400 shadow-[0_0_20px_rgba(16,185,129,0.5)]"></div>
                
                <div class="relative z-10 space-y-6">
                    <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mx-auto shadow-inner border-2 border-emerald-100 animate-bounce">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check"><path d="M20 6 9 17l-5-5"/></svg>
                    </div>
                    
                    <div>
                        <h3 class="text-2xl font-black text-gray-900 leading-tight">Berhasil Terkirim!</h3>
                        <p class="text-xs font-bold text-gray-400 mt-2 leading-relaxed italic uppercase tracking-widest">Suaramu membantu sesama pembaca</p>
                    </div>

                    <div class="pt-2">
                        <button @click="isSuccessModalOpen = false; window.location.reload();" class="w-full py-4 bg-gray-900 text-white text-[10px] font-black rounded-2xl shadow-xl active:scale-95 transition-all uppercase tracking-widest">
                            Selesai
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Upload Modal -->
        <div 
            x-show="isSummaryModalOpen" 
            class="fixed inset-0 z-50 flex items-start justify-center p-6 overflow-y-auto"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none;"
        >
            <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-xl" @click="closeSummaryModal()"></div>
            <div 
                x-show="isSummaryModalOpen"
                class="relative bg-white w-full max-w-sm rounded-[2.5rem] shadow-2xl p-8"
                x-transition:enter="transition-spring duration-500"
                x-transition:enter-start="opacity-0 scale-90 translate-y-8"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            >
                <div class="mb-6">
                    <h3 class="text-xl font-black text-gray-900 tracking-tight">Kumpulkan Rangkuman</h3>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Selesaikan tugas literasimu</p>
                </div>

                <form :action="selectedBorrowId ? `/member/borrows/${selectedBorrowId}/summary` : '#'" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <!-- Book Selection -->
                    <div>
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Pilih Buku</label>
                        <div class="relative group">
                            <select 
                                name="borrow_id" 
                                x-model="selectedBorrowId"
                                required 
                                class="w-full bg-gray-50 border-none rounded-2xl py-4 pl-5 pr-10 text-xs font-bold appearance-none focus:ring-2 focus:ring-emerald-500/20 transition-all text-gray-700"
                            >
                                <option value="" disabled selected>Pilih dari buku yang dipinjam...</option>
                                <template x-for="book in eligibleBooks" :key="book.borrow_id">
                                    <option :value="book.borrow_id" x-text="book.title"></option>
                                </template>
                            </select>
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none group-focus-within:text-emerald-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                            </div>
                        </div>
                        <template x-if="eligibleBooks.length === 0">
                            <p class="text-[9px] text-red-500 font-bold mt-2 ml-1">Kamu belum memiliki buku yang sudah dikembalikan untuk dirangkum.</p>
                        </template>
                    </div>

                    <!-- File Upload -->
                    <div>
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Upload File (PDF/Image)</label>
                        <div class="relative border-2 border-dashed border-gray-100 rounded-[2rem] p-8 text-center bg-gray-50/30 group hover:border-emerald-200 transition-colors">
                            <input type="file" name="file" required class="absolute inset-0 opacity-0 cursor-pointer z-10" @change="handleFileChange">
                            <div class="space-y-3">
                                <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center mx-auto text-gray-300 group-hover:text-emerald-500 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                                </div>
                                <p class="text-[11px] font-bold text-gray-500" x-text="fileName || 'Klik atau tarik file ke sini'"></p>
                                <p class="text-[9px] text-gray-300 font-medium tracking-wide italic">Maks. 5MB (PDF atau Foto)</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                         <button type="button" @click="closeSummaryModal()" class="flex-1 py-4 bg-gray-50 text-gray-400 text-[10px] font-black rounded-2xl active:scale-95 transition-all uppercase tracking-widest">Batal</button>
                         <button 
                            type="submit" 
                            :disabled="!selectedBorrowId"
                            class="flex-[2] py-4 bg-emerald-600 text-white text-[10px] font-black rounded-2xl shadow-xl shadow-emerald-200 active:scale-95 transition-all uppercase tracking-widest disabled:opacity-50"
                        >
                            Kumpulkan
                        </button>
                    </div>
                </form>
            </div>
        <!-- Literacy Journey Modal (Post-Loan Flow) -->
        <div 
            x-show="isLiteracyJourneyOpen" 
            class="fixed inset-0 z-[60] flex items-center justify-center p-6"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none;"
        >
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-2xl" @click="closeLiteracyJourney()"></div>
            
            <div 
                x-show="isLiteracyJourneyOpen"
                class="relative bg-white w-full max-w-sm rounded-[3rem] shadow-[0_40px_80px_-15px_rgba(0,0,0,0.3)] overflow-hidden border border-white/20"
                x-transition:enter="transition-spring duration-500"
                x-transition:enter-start="opacity-0 scale-90 translate-y-12"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            >
                <!-- Close Button -->
                <button @click="closeLiteracyJourney()" class="absolute top-6 right-6 z-30 p-2 text-gray-400 hover:text-gray-900 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>

                <!-- Stage 1: Motivation -->
                <div x-show="literacyStage === 1" class="p-10 text-center space-y-8 animate-fade-in">
                    <div class="relative inline-block">
                        <div class="w-24 h-24 bg-emerald-500 rounded-[2rem] flex items-center justify-center text-white shadow-2xl shadow-emerald-200 transform rotate-6 animate-bounce">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-party-popper"><path d="M5.8 11.3 2 22l10.7-3.8"/><path d="M4 3h.01"/><path d="M9.2 2.1 9 7"/><path d="M11 3h.01"/><path d="M15.2 3.1 16 7"/><path d="M13 2.1z"/><path d="M19.1 4.9 17 9"/><path d="M21 5h.01"/><path d="M20.9 9.1l-4.5 4.6"/><path d="M21 11h.01"/><path d="m11.5 15.5 1.5-2"/><path d="m5.5 13 2.5-1.5"/><path d="m15.5 19-3-2"/></svg>
                        </div>
                        <div class="absolute -top-4 -right-4 w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg transform -rotate-12 border border-emerald-100">
                             <span class="text-xl">🏆</span>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-3xl font-black text-gray-900 tracking-tight leading-tight">Luar Biasa!</h3>
                        <p class="text-sm font-medium text-gray-500 mt-4 leading-relaxed">
                            Kamu baru saja menyelesaikan buku <span class="text-emerald-600 font-bold" x-text="pendingBook?.title"></span>. Membaca adalah satu langkah menuju masa depan hebat!
                        </p>
                    </div>

                    <button @click="nextLiteracyStage()" class="w-full py-5 bg-emerald-600 text-white text-xs font-black rounded-2xl shadow-xl shadow-emerald-200 active:scale-95 transition-all uppercase tracking-[0.2em]">
                        Wah, Keren! Lanjut
                    </button>
                    
                    <p class="text-[9px] text-gray-300 font-bold uppercase tracking-widest">Satu tahap dari 3 selesai</p>
                </div>

                <!-- Stage 2: Summary -->
                <div x-show="literacyStage === 2" class="p-10 space-y-8 animate-fade-in">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><line x1="10" x2="8" y1="9" y2="9"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-gray-900 leading-tight">Tugas Rangkuman</h3>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Simpan poin penting bacaanmu</p>
                        </div>
                    </div>

                    <form @submit.prevent="submitSummary" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <div class="relative border-2 border-dashed border-gray-100 rounded-[2rem] p-8 text-center bg-gray-50/30 group hover:border-indigo-200 transition-colors">
                            <input type="file" name="file" required class="absolute inset-0 opacity-0 cursor-pointer z-10" @change="handleFileChange">
                            <div class="space-y-3">
                                <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center mx-auto text-gray-300 group-hover:text-indigo-500 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                                </div>
                                <p class="text-[11px] font-bold text-gray-500" x-text="fileName || 'Upload Rangkuman'"></p>
                            </div>
                        </div>

                        <div class="flex gap-3 pt-2">
                             <button type="button" @click="nextLiteracyStage()" class="flex-1 py-4 bg-gray-50 text-gray-400 text-[10px] font-black rounded-2xl active:scale-95 transition-all uppercase tracking-widest">Nanti Saja</button>
                             <button type="submit" class="flex-[2] py-4 bg-gray-900 text-white text-[10px] font-black rounded-2xl shadow-xl active:scale-95 transition-all uppercase tracking-widest">Upload & Lanjut</button>
                        </div>
                    </form>
                </div>

                <!-- Stage 3: Rating (Wajib) -->
                <div x-show="literacyStage === 3" class="p-10 space-y-8 animate-fade-in">
                    <div class="text-center">
                        <h3 class="text-2xl font-black text-gray-900 leading-tight">Beri Rating</h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-2">Bantu teman lain memilih buku ini</p>
                    </div>

                    <div class="flex justify-center gap-2">
                        <template x-for="i in 5">
                            <button @click="rating = i" class="transition-transform active:scale-90 duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" :fill="rating >= i ? '#FBBF24' : 'none'" :stroke="rating >= i ? '#FBBF24' : '#E5E7EB'" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            </button>
                        </template>
                    </div>

                    <div>
                        <textarea 
                            x-model="comment"
                            rows="4" 
                            placeholder="Apa kesanmu tentang buku ini?..."
                            class="w-full rounded-[1.5rem] border-gray-100 bg-gray-50 focus:border-emerald-500 focus:ring-emerald-500 transition-all text-sm py-4 px-5"
                        ></textarea>
                    </div>

                    <button 
                        @click="submitReview()" 
                        :disabled="rating === 0 || !comment"
                        class="w-full py-5 bg-emerald-600 text-white text-xs font-black rounded-2xl shadow-xl shadow-emerald-200 active:scale-95 transition-all uppercase tracking-[0.2em] disabled:opacity-50"
                    >
                        Kirim Ulasan
                    </button>
                    
                    <p class="text-[8px] text-center text-gray-300 font-bold uppercase tracking-widest italic animate-pulse">Popup ulasan tidak dapat ditutup sampai selesai.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- New Borrow Notification Pop-up -->
    <template x-if="newLoanData">
        <div 
            x-show="isNewBorrowModalOpen" 
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 scale-90 translate-y-10"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-90 translate-y-10"
            class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-emerald-950/40 backdrop-blur-md"
        >
            <div class="relative w-full max-w-sm animate-fade-in" @click.away="dismissNotification()">
                <!-- Icon Decoration (Outside overflow container to prevent clipping) -->
                <div class="absolute -top-12 left-1/2 -translate-x-1/2 w-24 h-24 bg-emerald-600 rounded-[2.5rem] flex items-center justify-center shadow-2xl shadow-emerald-200 border-4 border-white rotate-12 transition-spring hover:rotate-0 duration-500 z-10">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-book-check"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="m9 12 2 2 4-4"/></svg>
                </div>

                <!-- Inner Container (overflow-hidden to guarantee perfect rounded corners on bottom footer) -->
                <div class="bg-white w-full rounded-[3rem] overflow-hidden shadow-2xl shadow-emerald-900/20 border border-emerald-100">
                    <div class="p-8 text-center pt-24">
                        <div class="space-y-4">
                            <h3 class="text-2xl font-black text-gray-900 leading-tight tracking-tight uppercase" x-text="newLoanData.data.title">Peminjaman Berhasil</h3>
                            <p class="text-xs text-gray-500 font-medium leading-relaxed" x-text="newLoanData.data.message"></p>
                        </div>

                        <div class="mt-10 space-y-3">
                            <a 
                                :href="getCorrectActionUrl(newLoanData.data.action_url)" 
                                class="block w-full py-5 bg-emerald-600 text-white text-[10px] font-black rounded-2xl shadow-xl shadow-emerald-200 active:scale-95 transition-all uppercase tracking-[0.25em]"
                            >
                                Lihat Pinjamanku
                            </a>
                            <button 
                                @click="dismissNotification()" 
                                class="block w-full py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-colors"
                            >
                                Nanti Saja
                            </button>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50/80 px-8 py-4 border-t border-gray-50 flex items-center justify-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Digital Library Sync</span>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <script>
        function dashboardApp() {
            return {
                isBookModalOpen: false,
                isSummaryModalOpen: false,
                isLiteracyJourneyOpen: false,
                isSuccessModalOpen: false,
                isNewBorrowModalOpen: false,
                newLoanData: @json($newLoanNotification),
                literacyStage: 1, // 1: Motivation, 2: Summary, 3: Review
                memberType: '{{ auth()->user()->member_type }}',
                pendingBook: null,
                selectedBook: null,
                selectedBorrowId: null,
                eligibleBooks: [],
                fileName: '',
                rating: 0,
                comment: '',

                init() {
                    console.log("Dashboard App Ready");
                    
                    // Reset status awal
                    document.body.classList.remove('lock-scroll', 'overflow-hidden');
                    document.documentElement.classList.remove('overflow-hidden');
                    
                    // Individual watchers for each modal state
                    this.$watch('isBookModalOpen', () => this.syncScroll());
                    this.$watch('isSummaryModalOpen', () => this.syncScroll());
                    this.$watch('isLiteracyJourneyOpen', () => this.syncScroll());
                    this.$watch('isSuccessModalOpen', () => this.syncScroll());
                    this.$watch('isNewBorrowModalOpen', () => this.syncScroll());

                    this.checkPendingActions();

                    // Tampilkan pop-up peminjaman baru jika ada
                    if (this.newLoanData) {
                        setTimeout(() => {
                            this.isNewBorrowModalOpen = true;
                        }, 500);
                    }
                },

                syncScroll() {
                    // Selalu pastikan body dapat di-scroll dengan lancar
                    document.body.classList.remove('lock-scroll', 'overflow-hidden');
                    document.documentElement.classList.remove('overflow-hidden');
                },

                closeLiteracyJourney() {
                    this.isLiteracyJourneyOpen = false;
                    this.syncScroll();
                },

                getCorrectActionUrl(rawUrl) {
                    if (!rawUrl) return "{{ url('/member/loans') }}";
                    try {
                        const urlObj = new URL(rawUrl);
                        const match = urlObj.pathname.match(/\/member\/.*/);
                        if (match) {
                            const currentPath = window.location.pathname;
                            let basePath = '';
                            const subfolderMatch = currentPath.match(/.*?(?=\/(dashboard|member|books|profile|login|api|$))/);
                            if (subfolderMatch) {
                                basePath = subfolderMatch[0];
                            }
                            return window.location.origin + basePath + match[0];
                        }
                    } catch(e) {
                        console.error("Error parsing action_url:", e);
                    }
                    return "{{ url('/member/loans') }}";
                },

                async checkPendingActions() {
                    try {
                        const response = await fetch("{{ url('/api/pending-post-loan-action') }}");
                        if (!response.ok) return;
                        const data = await response.json();
                        if (data.has_pending) {
                            this.pendingBook = data.book;
                            this.selectedBorrowId = data.borrow_id;
                            // Tidak otomatis membuka modal yang mengunci layar
                        }
                    } catch (e) {
                        console.error("Gagal cek pending action:", e);
                    }
                },

                nextLiteracyStage() {
                    if (this.literacyStage === 1 && this.memberType === 'teacher') {
                        this.literacyStage = 3;
                        return;
                    }
                    if (this.literacyStage < 3) {
                        this.literacyStage++;
                    }
                },

                async submitSummary(e) {
                    const formData = new FormData(e.target);
                    
                    try {
                        const response = await fetch(`{{ url('/member/borrows') }}/${this.selectedBorrowId}/summary`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        if (response.ok) {
                            this.fileName = '';
                            this.nextLiteracyStage();
                        } else {
                            const data = await response.json();
                            alert(data.message || "Gagal mengunggah rangkuman.");
                        }
                    } catch (e) {
                        console.error("Gagal kirim summary:", e);
                    }
                },

                async submitDashboardReview() {
                    if (this.rating === 0) return alert("Beri rating terlebih dahulu!");
                    
                    try {
                        const response = await fetch(`{{ url('/member/books') }}/${this.selectedBook.id}/review`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                rating: this.rating,
                                comment: this.comment
                            })
                        });

                        const data = await response.json();
                        if (response.ok) {
                            this.isSuccessModalOpen = true;
                            // Optionally refresh selectedBook data to show the new pending review
                            this.openBook(this.selectedBook.id); 
                            this.rating = 0;
                            this.comment = '';
                        } else {
                            alert(data.message || "Gagal mengirim ulasan.");
                        }
                    } catch (e) {
                        console.error("Gagal kirim review:", e);
                    }
                },

                async dismissNotification() {
                    if (!this.newLoanData) return;
                    
                    try {
                        await fetch(`{{ url('/member/notifications') }}/${this.newLoanData.id}/read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        });
                        this.isNewBorrowModalOpen = false;
                        this.newLoanData = null;
                    } catch (e) {
                        console.error("Gagal dismiss notification:", e);
                        this.isNewBorrowModalOpen = false;
                    }
                },

                async submitReview() {
                    if (this.rating === 0) return alert("Beri rating terlebih dahulu!");
                    
                    try {
                        const response = await fetch(`{{ url('/books') }}/${this.pendingBook.id}/reviews`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                rating: this.rating,
                                comment: this.comment
                            })
                        });

                        if (response.ok) {
                            this.isLiteracyJourneyOpen = false;
                            alert("Terima kasih atas ulasanmu! Teruslah membaca.");
                        }
                    } catch (e) {
                        console.error("Gagal kirim review:", e);
                    }
                },

                async openBook(bookOrId) {
                    const bookId = (typeof bookOrId === 'object' && bookOrId !== null) ? bookOrId.id : bookOrId;
                    if (!bookId) {
                        alert("ID buku tidak valid.");
                        return;
                    }
                    this.selectedBook = null;
                    this.isBookModalOpen = true;
                    try {
                        const response = await fetch(`{{ url('/api/books') }}/${bookId}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (response.status === 401) {
                            alert("Sesi Anda telah berakhir, silakan login kembali.");
                            window.location.href = '/login';
                            return;
                        }
                        if (!response.ok) {
                            const errText = await response.text().catch(() => '');
                            throw new Error(`HTTP ${response.status} ${errText ? '- ' + errText.substring(0, 80) : ''}`);
                        }
                        const data = await response.json();
                        this.selectedBook = data.book;
                    } catch (e) {
                        console.error("Error openBook:", e);
                        this.isBookModalOpen = false;
                        alert("Gagal memuat detail buku: " + e.message);
                    }
                },

                closeBook() {
                    this.isBookModalOpen = false;
                },

                async openSummaryModal() {
                    try {
                        const response = await fetch("{{ url('/api/eligible-books-for-summary') }}");
                        this.eligibleBooks = await response.json();
                        this.isSummaryModalOpen = true;
                    } catch (e) {
                        console.error("Gagal ambil daftar buku:", e);
                    }
                },

                closeSummaryModal() {
                    this.isSummaryModalOpen = false;
                    this.fileName = '';
                },

                handleFileChange(e) {
                    if (e.target.files.length > 0) {
                        this.fileName = e.target.files[0].name;
                    }
                },

                getStatusClass(status) {
                    const map = {
                        'available': 'bg-emerald-50 text-emerald-600',
                        'borrowed': 'bg-amber-50 text-amber-600',
                        'reserved': 'bg-indigo-50 text-indigo-600',
                        'lost': 'bg-red-50 text-red-600'
                    };
                    return map[status] || 'bg-gray-50 text-gray-400';
                },

                getStatusText(status) {
                    const map = {
                        'available': 'Tersedia',
                        'borrowed': 'Dipinjam',
                        'reserved': 'Dipesan',
                        'lost': 'Hilang'
                    };
                    return map[status] || (status ? status.toUpperCase() : 'UNKNOWN');
                }
            }
        }
    </script>

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        .transition-spring {
            transition-timing-function: cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .py-4.5 {
            padding-top: 1.125rem;
            padding-bottom: 1.125rem;
        }

        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .lock-scroll {
            overflow: hidden !important;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.4s ease-out forwards;
        }
    </style>
</x-member-layout>

