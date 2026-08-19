<x-member-layout>
    <div x-data="catalogApp()" x-init="init()" class="pb-24">
        <!-- Sticky Search Header -->
        <div class="sticky top-0 z-30 bg-white/80 backdrop-blur-md px-6 py-4 border-b border-gray-100">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="relative group flex-1">
                    <input 
                        type="text" 
                        x-model="searchQuery" 
                        placeholder="Cari judul, penulis, atau kategori..." 
                        class="w-full pl-12 pr-4 py-4 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-emerald-500/20 transition-all font-medium"
                    >
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                </div>

                <!-- Status Filter Dropdown -->
                <div class="relative min-w-[160px]">
                    <select 
                        x-model="activeStatus"
                        class="w-full h-full py-4 px-6 bg-gray-50 border-none rounded-2xl text-xs font-black uppercase tracking-widest text-emerald-700 focus:ring-2 focus:ring-emerald-500/20 appearance-none cursor-pointer"
                    >
                        <option value="all">Semua Buku</option>
                        <option value="available">Tersedia</option>
                        <option value="borrowed">Dipinjam</option>
                        <option value="reserved">Dipesan</option>
                        <option value="lost">Hilang</option>
                    </select>
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-emerald-400">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 mt-6">
            <!-- Error Alert -->
            <template x-if="errorMessage">
                <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-2xl flex flex-col items-center gap-2 text-center animate-fade-in">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-red-500"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                    <div>
                        <p class="text-sm font-black text-red-800">Gagal Memuat Katalog Buku</p>
                        <p class="text-[10px] text-red-600 font-bold mt-1" x-text="errorMessage"></p>
                    </div>
                    <button @click="init()" class="mt-2 px-6 py-2 bg-red-600 text-white text-[10px] font-black rounded-full shadow-lg shadow-red-200 uppercase tracking-widest active:scale-95 transition-all">Coba Lagi</button>
                </div>
            </template>

            <!-- Loading State -->
            <template x-if="isLoading">
                <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
                    <template x-for="i in 6">
                        <div class="animate-pulse">
                            <div class="bg-gray-200 aspect-[3/4] rounded-2xl mb-3"></div>
                            <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                            <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Book Grid (Visible when not loading) -->
            <div x-show="!isLoading && !errorMessage" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <template x-for="book in filteredBooks" :key="book.id">
                    <a :href="`{{ url('/member/books') }}/${book.id}`" class="group active:scale-95 transition-all duration-200 cursor-pointer block">
                        <div class="relative aspect-[3/4] rounded-[2rem] overflow-hidden shadow-sm border border-gray-100 mb-3 bg-gray-50">
                            <img :src="book.cover_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(book.title)}&background=f0fdf4&color=15803d&size=512`" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                                 :alt="book.title">
                            
                            <!-- Small Meta Info (Rating & Category) -->
                            <div class="absolute top-3 left-3 flex flex-col gap-2">
                                <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-[9px] font-black rounded-full shadow-sm text-gray-900 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="currentColor" class="text-amber-400"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    <span x-text="book.avg_rating || '5.0'"></span>
                                </span>
                            </div>

                            <div class="absolute bottom-3 left-3 right-3">
                                <span :class="getStatusClass(book.status)" class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-wider shadow-sm">
                                    <span x-text="getStatusText(book.status)"></span>
                                </span>
                            </div>
                        </div>
                        <h3 class="font-bold text-gray-900 text-xs line-clamp-1 px-1" x-text="book.title"></h3>
                        <p class="text-[10px] text-gray-500 px-1 mt-0.5" x-text="book.author"></p>
                    </a>
                </template>
            </div>

            <!-- Empty State -->
            <div x-show="!isLoading && !errorMessage && filteredBooks.length === 0" class="py-20 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-gray-300"><path d="M12 7v5l3 3"/><circle cx="12" cy="12" r="10"/><path d="m16 21-2-2"/></svg>
                </div>
                <p class="text-sm font-bold text-gray-400">Buku tidak ditemukan</p>
                <button @click="searchQuery = ''" class="mt-2 text-xs font-bold text-emerald-600">Reset Pencarian</button>
            </div>
        </div>

        <!-- Truly Elegant Premium Book Detail Modal -->
        <div 
            x-show="isModalOpen" 
            class="fixed inset-0 z-50 flex items-center justify-center p-6"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none;"
        >
            <!-- Luxurious Backdrop Blur -->
            <div 
                class="absolute inset-0 bg-gray-900/40 backdrop-blur-xl"
                @click="closeBook()"
            ></div>

            <!-- Elegant Modal Card -->
            <div 
                x-show="isModalOpen"
                class="relative bg-white/95 w-full max-w-sm rounded-[2.5rem] shadow-[0_32px_64px_-16px_rgba(0,0,0,0.25)] overflow-hidden border border-white/20"
                x-transition:enter="transition-spring duration-500"
                x-transition:enter-start="opacity-0 scale-90 translate-y-8"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                @click.away="closeBook()"
            >
                <!-- Decorative Top Graphic -->
                <div class="h-32 bg-gradient-to-br from-emerald-50 to-white relative overflow-hidden">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-emerald-100/40 via-transparent to-transparent"></div>
                    
                    <!-- Close Button (Minimalist) -->
                    <button @click="closeBook()" class="absolute top-6 right-6 z-10 p-2 text-gray-400 hover:text-gray-900 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>

                <!-- Floating Cover Area -->
                <div class="px-8 -mt-20 relative z-10 flex flex-col items-center text-center">
                    <div class="w-32 aspect-[3/4] bg-white rounded-2xl shadow-[0_20px_40px_-10px_rgba(0,0,0,0.3)] border-[6px] border-white overflow-hidden transform transition-transform duration-500 hover:scale-105">
                        <img :src="selectedBook?.cover_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(selectedBook?.title)}&background=f0fdf4&color=15803d&size=512`" 
                             class="w-full h-full object-cover">
                    </div>

                    <div class="mt-6">
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

                <!-- Content Body -->
                <div class="p-8 pt-0 space-y-6 max-h-[60vh] overflow-y-auto no-scrollbar">
                    <!-- Info Grid -->
                    <div class="grid grid-cols-2 gap-x-6 gap-y-4 mb-8">
                        <div class="flex flex-col">
                            <p class="text-[8px] font-black text-gray-300 uppercase tracking-widest mb-1 tracking-[0.2em]">Kategori</p>
                            <p class="text-[11px] font-bold text-gray-700" x-text="selectedBook?.category || '-'"></p>
                        </div>
                        <div class="flex flex-col">
                            <p class="text-[8px] font-black text-gray-300 uppercase tracking-widest mb-1 tracking-[0.2em]">Rak / Lokasi</p>
                            <p class="text-[11px] font-bold text-gray-700" x-text="selectedBook?.rack_code || '-'"></p>
                        </div>
                        <div class="flex flex-col">
                            <p class="text-[8px] font-black text-gray-300 uppercase tracking-widest mb-1 tracking-[0.2em]">Halaman</p>
                            <p class="text-[11px] font-bold text-gray-700" x-text="`${selectedBook?.pages || '0'} Hal`"></p>
                        </div>
                        <div class="flex flex-col">
                            <p class="text-[8px] font-black text-gray-300 uppercase tracking-widest mb-1 tracking-[0.2em]">Terdaftar Sejak</p>
                            <p class="text-[11px] font-bold text-gray-700" x-text="selectedBook?.added_at || '-'"></p>
                        </div>
                        <div class="flex flex-col col-span-2">
                             <p class="text-[8px] font-black text-gray-300 uppercase tracking-widest mb-1 tracking-[0.2em]">Ketersediaan Stok</p>
                             <div class="flex items-center gap-2">
                                 <span class="text-[11px] font-black" :class="selectedBook?.stock_available > 0 ? 'text-emerald-600' : 'text-red-500'" x-text="`${selectedBook?.stock_available || 0} Tersedia`"></span>
                                 <span class="text-[10px] text-gray-300">/</span>
                                 <span class="text-[10px] font-bold text-gray-500" x-text="`${selectedBook?.stock_total || 0} Total Buku`"></span>
                             </div>
                        </div>
                    </div>

                    <!-- Borrow History Section -->
                    <div class="space-y-4 py-6 border-y border-gray-100/50" x-show="selectedBook?.borrow_history?.length">
                        <div class="flex items-center justify-between">
                            <h4 class="text-[10px] font-black text-gray-900 uppercase tracking-widest text-emerald-600">Jejak Pembaca</h4>
                            <span class="text-[8px] font-bold text-gray-300 uppercase tracking-widest">5 Peminjam Terakhir</span>
                        </div>
                        <div class="space-y-3">
                            <template x-for="(hist, index) in selectedBook?.borrow_history || []" :key="index">
                                <div class="flex items-center justify-between p-3 bg-gray-50/30 rounded-2xl border border-gray-100/50">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm border border-gray-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-black text-gray-900" x-text="hist.user_name"></p>
                                            <p class="text-[8px] font-bold text-gray-400 uppercase tracking-tight" x-text="hist.borrow_date"></p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-[8px] font-black px-2 py-0.5 rounded-lg border border-gray-100 text-gray-400 uppercase tracking-widest" x-text="hist.return_date"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Reader Reviews Section -->
                    <div class="space-y-4" x-show="selectedBook?.reviews?.length">
                        <div class="flex items-center justify-between">
                            <h4 class="text-[10px] font-black text-gray-900 uppercase tracking-widest">Ulasan Pembaca</h4>
                            <span class="text-[8px] font-bold text-gray-300" x-text="`${selectedBook?.reviews?.length || 0} ulasan`"></span>
                        </div>
                        <div class="space-y-3">
                            <template x-for="rev in selectedBook?.reviews || []" :key="rev.id">
                                <div class="p-4 bg-gray-50/50 rounded-2xl border border-gray-50">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-[10px] font-black text-gray-900" x-text="rev.user_name"></p>
                                        <div class="flex text-amber-400">
                                            <template x-for="i in 5">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="8" height="8" viewBox="0 0 24 24" :fill="rev.rating >= i ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                            </template>
                                        </div>
                                    </div>
                                    <p class="text-[11px] text-gray-500 leading-relaxed font-medium" x-text="rev.comment"></p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Action Area -->
                    <div class="sticky bottom-0 pt-4 bg-white/95 backdrop-blur-sm -mx-8 px-8 pb-8">
                        <form :action="`{{ url('/member/books') }}/${selectedBook?.id}/queue`" method="POST">
                            @csrf
                            <button 
                                type="submit"
                                :disabled="selectedBook?.status === 'lost'"
                                class="w-full py-4.5 bg-gray-900 text-white text-[10px] font-black rounded-2xl shadow-2xl active:scale-[0.98] transition-all uppercase tracking-[0.2em] relative overflow-hidden group disabled:opacity-50"
                            >
                                <span class="relative z-10" x-text="selectedBook?.status === 'available' ? 'Pinjam Sekarang' : 'Masuk Antrean'"></span>
                                <div class="absolute inset-0 bg-gradient-to-r from-emerald-600 to-teal-600 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function catalogApp() {
            return {
                books: [],
                searchQuery: '',
                activeStatus: 'all',
                isLoading: false,
                errorMessage: '',
                
                // Modal State
                isModalOpen: false,
                selectedBook: null,
                isDetailLoading: false,

                init() {
                    this.fetchBooks();
                    this.$watch('activeStatus', () => this.fetchBooks());
                },

                async fetchBooks() {
                    this.isLoading = true;
                    this.errorMessage = '';
                    try {
                        const baseUrl = '{{ route("api.books.index") }}';
                        const url = new URL(baseUrl, window.location.origin);
                        if (this.activeStatus !== 'all') {
                            url.searchParams.set('status', this.activeStatus);
                        }
                        
                        const response = await fetch(url);
                        if (!response.ok) throw new Error('Gagal mengambil data buku');
                        this.books = await response.json();
                    } catch (err) {
                        this.errorMessage = err.message;
                    } finally {
                        this.isLoading = false;
                    }
                },

                async openBook(bookOrId) {
                    const bookId = (typeof bookOrId === 'object' && bookOrId !== null) ? bookOrId.id : bookOrId;
                    if (!bookId) {
                        alert("ID buku tidak valid.");
                        return;
                    }
                    this.selectedBook = null;
                    this.isModalOpen = true;
                    document.body.classList.add('lock-scroll');
                    
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
                        this.isModalOpen = false;
                        document.body.classList.remove('lock-scroll');
                        alert("Gagal memuat detail buku: " + e.message);
                    }
                },

                closeBook() {
                    this.isModalOpen = false;
                    document.body.classList.remove('lock-scroll');
                },

                get filteredBooks() {
                    if (!this.searchQuery) return this.books;
                    const q = this.searchQuery.toLowerCase();
                    return this.books.filter(b => 
                        b.title.toLowerCase().includes(q) || 
                        b.author.toLowerCase().includes(q) || 
                        (b.category && b.category.toLowerCase().includes(q))
                    );
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
                        'borrowed': 'Sedang Dipinjam',
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
        
        /* Custom Spring Animation */
        .transition-spring {
            transition-timing-function: cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .lock-scroll {
            overflow: hidden !important;
        }

        .py-4.5 {
            padding-top: 1.125rem;
            padding-bottom: 1.125rem;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .animate-fade-in { animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</x-member-layout>
