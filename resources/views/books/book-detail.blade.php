<x-member-layout>
    <div x-data="ecommerceBookApp(@js($bookData), @js($relatedBooks))" class="pb-36 min-h-screen bg-slate-50/60">
        <!-- Sticky E-Commerce Header -->
        <header class="sticky top-0 z-30 bg-white border-b border-gray-100 px-4 py-3 sm:px-6 flex items-center justify-between shadow-xs" style="background-color: #ffffff !important; backdrop-filter: none !important; -webkit-backdrop-filter: none !important; opacity: 1 !important;">
            <div class="flex items-center gap-3">
                <a href="{{ route('member.books.index') }}" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 transition-all active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                </a>
                <div>
                    <h1 class="text-sm font-black text-gray-900 tracking-tight line-clamp-1" x-text="book?.title || 'Detail Buku'"></h1>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest" x-text="book?.category || 'Katalog Buku'"></p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button @click="shareProduct()" class="w-9 h-9 rounded-full bg-gray-50 flex items-center justify-center text-gray-600 hover:bg-emerald-50 hover:text-emerald-600 transition-all active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" x2="15.42" y1="10.68" y2="6.32"/><line x1="8.59" x2="15.42" y1="13.32" y2="17.68"/></svg>
                </button>
            </div>
        </header>

        <!-- E-Commerce Product Showcase -->
        <div class="max-w-4xl mx-auto px-4 pt-6 space-y-6">
            <!-- Main Product Card -->
            <div class="bg-white rounded-[2.5rem] p-6 md:p-8 border border-gray-100 shadow-sm flex flex-col md:flex-row gap-8">
                <!-- Left: Book Cover Image Stage -->
                <div class="w-full md:w-72 shrink-0 flex flex-col items-center">
                    <div class="relative w-full aspect-[3/4] rounded-[2rem] overflow-hidden bg-gradient-to-tr from-gray-100 to-slate-50 border border-gray-100 shadow-xl group">
                        <img 
                            :src="book.cover_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(book.title)}&background=f0fdf4&color=15803d&size=512`" 
                            :alt="book.title"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                        >
                        
                        <!-- Badges Overlay -->
                        <div class="absolute top-3 left-3 flex flex-col gap-2">
                            <span :class="getStatusClass(book.status)" class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest shadow-xs">
                                <span x-text="getStatusText(book.status)"></span>
                            </span>
                        </div>

                        <div class="absolute bottom-3 right-3 bg-black/60 backdrop-blur-md px-3 py-1 rounded-full text-white text-[9px] font-mono font-bold">
                            <span x-text="book.code"></span>
                        </div>
                    </div>

                    <!-- Quick Category Tag -->
                    <div class="mt-4 flex items-center gap-2">
                        <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full text-[10px] font-black uppercase tracking-widest" x-text="book.category || 'NOVEL'"></span>
                        <template x-if="book.label_color">
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-[10px] font-bold" x-text="'Label: ' + book.label_color"></span>
                        </template>
                    </div>
                </div>

                <!-- Right: Product Info & Details -->
                <div class="flex-1 flex flex-col justify-between space-y-6">
                    <div>
                        <!-- Author & Title -->
                        <p class="text-xs font-bold text-emerald-600 uppercase tracking-widest mb-1" x-text="book.author || 'Penulis Tidak Diketahui'"></p>
                        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 leading-tight tracking-tight mb-3" x-text="book.title"></h1>

                        <!-- Rating & Reviews Bar -->
                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex items-center gap-1 bg-amber-50 border border-amber-100 px-3 py-1.5 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" class="text-amber-400"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                <span class="text-xs font-black text-amber-800" x-text="book.avg_rating || '5.0'"></span>
                            </div>
                            <a href="#reviews-section" @click="activeTab = 'reviews'" class="text-xs font-bold text-gray-500 hover:text-emerald-600 underline">
                                <span x-text="book.reviews_count || 0"></span> Ulasan Anggota
                            </a>
                            <span class="text-gray-300">•</span>
                            <span class="text-xs font-bold text-gray-500">
                                <span x-text="book.borrows_count || 0"></span>x Dipinjam
                            </span>
                        </div>

                        <!-- E-Commerce Highlight Cards Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 bg-slate-50/80 rounded-2xl border border-slate-100 mb-6">
                            <div class="text-center sm:text-left">
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Stok Eksemplar</p>
                                <p class="text-sm font-black text-gray-900 mt-0.5">
                                    <span x-text="book.stock_available"></span> / <span x-text="book.stock_total" class="text-gray-400"></span>
                                </p>
                            </div>
                            <div class="text-center sm:text-left border-l border-gray-200/60 pl-3">
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Lokasi Rak</p>
                                <p class="text-sm font-black text-emerald-600 mt-0.5" x-text="book.rack_code || '-'"></p>
                            </div>
                            <div class="text-center sm:text-left border-l border-gray-200/60 pl-3">
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Jumlah Hal</p>
                                <p class="text-sm font-black text-gray-900 mt-0.5" x-text="book.pages ? book.pages + ' hlm' : '-'"></p>
                            </div>
                            <div class="text-center sm:text-left border-l border-gray-200/60 pl-3">
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Kode ISBN</p>
                                <p class="text-xs font-mono font-bold text-gray-700 mt-1 truncate" x-text="book.isbn || 'N/A'"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Real-time Queue / Stock Banner -->
                    <div class="p-4 rounded-2xl border transition-all mb-4"
                         :class="book.stock_available > 0 ? 'bg-emerald-50/70 border-emerald-100 text-emerald-900' : 'bg-amber-50/70 border-amber-100 text-amber-900'">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0"
                                 :class="book.stock_available > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-black">
                                    <template x-if="book.user_queue_id">
                                        <span class="text-indigo-600">Kamu Dalam Antrean Booking (Urutan ke-<span x-text="book.user_queue_position"></span>)</span>
                                    </template>
                                    <template x-if="!book.user_queue_id && book.stock_available > 0">
                                        <span>Buku Siap Dipinjam Langsung!</span>
                                    </template>
                                    <template x-if="!book.user_queue_id && book.stock_available <= 0">
                                        <span>Stok Habis — Ada <span x-text="book.active_queues_count || 0"></span> Siswa Mengantre</span>
                                    </template>
                                </p>
                                <p class="text-[10px] opacity-80 mt-0.5">
                                    <template x-if="book.user_queue_id">
                                        <span>Estimasi buku siap untukmu: <strong class="text-indigo-700" x-text="book.user_queue_estimated_date"></strong>. Batas ambil 2 hari saat siap.</span>
                                    </template>
                                    <template x-if="!book.user_queue_id && book.stock_available > 0">
                                        <span>Klik tombol pinjam di bawah untuk reservasi buku ini di perpustakaan.</span>
                                    </template>
                                    <template x-if="!book.user_queue_id && book.stock_available <= 0">
                                        <span>Estimasi siap jika antre sekarang: <strong class="text-amber-800" x-text="book.estimated_for_next_queue"></strong> (Antrean ke-<span x-text="(book.active_queues_count || 0) + 1"></span>).</span>
                                    </template>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- PROMINENT BORROW ACTION BUTTON (INSIDE CARD) -->
                    <template x-if="book.user_queue_id">
                        <form :action="`{{ url('/member/queues') }}/${book.user_queue_id}/cancel`" method="POST" class="w-full" onsubmit="return confirm('Batalkan booking buku ini? Posisi antreanmu akan dilepas.')">
                            @csrf
                            <button 
                                type="submit" 
                                class="w-full py-4 px-6 bg-rose-50 hover:bg-rose-100 text-rose-700 border-2 border-rose-200 rounded-2xl text-xs sm:text-sm font-black uppercase tracking-widest shadow-sm flex items-center justify-center gap-3 active:scale-98 transition-all cursor-pointer"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>
                                <span>❌ BATALKAN BOOKING ANTREAN INI</span>
                            </button>
                        </form>
                    </template>

                    <template x-if="!book.user_queue_id">
                        <form :action="`{{ url('/member/books') }}/${book.id}/queue`" method="POST" class="w-full">
                            @csrf
                            <button 
                                type="submit" 
                                class="w-full py-4 px-6 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-2xl text-xs sm:text-sm font-black uppercase tracking-widest shadow-xl shadow-emerald-600/30 flex items-center justify-center gap-3 active:scale-98 transition-all cursor-pointer"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M8 6h10"/><path d="M8 10h10"/><path d="M8 14h10"/></svg>
                                <span x-text="book.status === 'available' ? '📌 PINJAM / BOOKING BUKU INI' : '⏳ MASUK ANTREAN BOOKING'"></span>
                            </button>
                        </form>
                    </template>
                </div>
            </div>

            <!-- Product Tabs Navigation -->
            <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden" id="reviews-section">
                <div class="flex border-b border-gray-100 bg-slate-50/50">
                    <button 
                        @click="activeTab = 'info'" 
                        :class="activeTab === 'info' ? 'bg-white text-emerald-600 border-b-2 border-emerald-600 font-black shadow-xs' : 'text-gray-400 font-bold hover:text-gray-600'" 
                        class="flex-1 py-4 text-xs uppercase tracking-wider transition-all"
                    >
                        Informasi & Spesifikasi
                    </button>
                    <button 
                        @click="activeTab = 'reviews'" 
                        :class="activeTab === 'reviews' ? 'bg-white text-emerald-600 border-b-2 border-emerald-600 font-black shadow-xs' : 'text-gray-400 font-bold hover:text-gray-600'" 
                        class="flex-1 py-4 text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-1.5"
                    >
                        <span>Ulasan Pembaca</span>
                        <span class="px-2 py-0.5 text-[9px] bg-emerald-100 text-emerald-700 rounded-full" x-text="book.reviews_count || 0"></span>
                    </button>
                    <template x-if="book.can_review">
                        <button 
                            @click="activeTab = 'write'" 
                            :class="activeTab === 'write' ? 'bg-white text-emerald-600 border-b-2 border-emerald-600 font-black shadow-xs' : 'text-gray-400 font-bold hover:text-gray-600'" 
                            class="flex-1 py-4 text-xs uppercase tracking-wider transition-all"
                        >
                            ✍️ Tulis Ulasan
                        </button>
                    </template>
                </div>

                <!-- Tab Details Body -->
                <div class="p-6 md:p-8">
                    <!-- Tab 1: Info & Specs -->
                    <div x-show="activeTab === 'info'" class="space-y-8 animate-fade-in">
                        <div>
                            <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-4">Spesifikasi Detail Produk</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="p-4 bg-slate-50 rounded-2xl flex justify-between items-center">
                                    <span class="text-xs font-bold text-gray-500">Judul Lengkap</span>
                                    <span class="text-xs font-black text-gray-900 text-right" x-text="book.title"></span>
                                </div>
                                <div class="p-4 bg-slate-50 rounded-2xl flex justify-between items-center">
                                    <span class="text-xs font-bold text-gray-500">Penulis</span>
                                    <span class="text-xs font-black text-gray-900 text-right" x-text="book.author"></span>
                                </div>
                                <div class="p-4 bg-slate-50 rounded-2xl flex justify-between items-center">
                                    <span class="text-xs font-bold text-gray-500">Kode Buku</span>
                                    <span class="text-xs font-mono font-black text-emerald-700" x-text="book.code"></span>
                                </div>
                                <div class="p-4 bg-slate-50 rounded-2xl flex justify-between items-center">
                                    <span class="text-xs font-bold text-gray-500">Kategori Utama</span>
                                    <span class="text-xs font-black text-gray-900" x-text="book.category || 'Umum'"></span>
                                </div>
                                <div class="p-4 bg-slate-50 rounded-2xl flex justify-between items-center">
                                    <span class="text-xs font-bold text-gray-500">Rak Penyimpanan</span>
                                    <span class="text-xs font-black text-emerald-600" x-text="book.rack_code || 'TBA'"></span>
                                </div>
                                <div class="p-4 bg-slate-50 rounded-2xl flex justify-between items-center">
                                    <span class="text-xs font-bold text-gray-500">Jumlah Halaman</span>
                                    <span class="text-xs font-black text-gray-900" x-text="book.pages + ' Halaman'"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Customer Reviews -->
                    <div x-show="activeTab === 'reviews'" class="space-y-6 animate-fade-in">
                        <template x-if="!book.reviews || book.reviews.length === 0">
                            <div class="py-12 text-center bg-slate-50/50 rounded-2xl border border-dashed border-gray-200">
                                <p class="text-gray-400 text-xs font-bold">Belum ada ulasan untuk buku ini.</p>
                            </div>
                        </template>

                        <div class="space-y-4">
                            <template x-for="review in book.reviews" :key="review.id">
                                <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100/80">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-emerald-600 text-white font-black text-xs flex items-center justify-center uppercase shadow-xs" x-text="review.user_name.substring(0,2)"></div>
                                            <div>
                                                <p class="text-xs font-black text-gray-900" x-text="review.user_name"></p>
                                                <p class="text-[9px] font-bold text-gray-400" x-text="review.created_at"></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center text-amber-400 gap-0.5">
                                            <template x-for="i in review.rating">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                            </template>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-700 leading-relaxed font-medium" x-text="review.comment"></p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Tab 3: Write Review -->
                    <div x-show="activeTab === 'write'" class="animate-fade-in">
                        <form :action="`{{ url('/member/books') }}/${book.id}/review`" method="POST" class="space-y-5 max-w-lg">
                            @csrf
                            <div>
                                <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">Beri Rating Bintang</label>
                                <div class="flex gap-2">
                                    <template x-for="i in 5">
                                        <button 
                                            type="button" 
                                            @click="userRating = i"
                                            class="w-12 h-12 rounded-2xl flex items-center justify-center transition-all shadow-xs"
                                            :class="userRating >= i ? 'bg-amber-100 text-amber-500 scale-105' : 'bg-gray-100 text-gray-300'"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                        </button>
                                    </template>
                                    <input type="hidden" name="rating" :value="userRating">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">Ulasan Anda</label>
                                <textarea 
                                    name="comment" 
                                    rows="4" 
                                    placeholder="Tulis ulasan Anda mengenai pengalaman membaca buku ini..."
                                    class="w-full bg-slate-50 border border-gray-200 rounded-2xl text-xs p-4 focus:ring-2 focus:ring-emerald-500/20 focus:bg-white transition-all outline-none"
                                    required
                                ></textarea>
                            </div>
                            <button type="submit" class="px-8 py-3.5 bg-emerald-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-md hover:bg-emerald-700 active:scale-95 transition-all">
                                Kirim Ulasan
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Related Products Carousel/Grid -->
            <template x-if="relatedBooks && relatedBooks.length > 0">
                <div class="pt-6">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider mb-4 px-1">Buku Terkait Lainnya</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                        <template x-for="rb in relatedBooks" :key="rb.id">
                            <a :href="`{{ url('/member/books') }}/${rb.id}`" class="bg-white p-3 rounded-2xl border border-gray-100 shadow-xs hover:border-emerald-200 group active:scale-95 transition-all">
                                <div class="aspect-[3/4] rounded-xl overflow-hidden bg-slate-100 mb-2">
                                    <img :src="rb.cover_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(rb.title)}&background=f0fdf4&color=15803d&size=512`" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                </div>
                                <h4 class="text-xs font-black text-gray-900 line-clamp-1 group-hover:text-emerald-600 transition-colors" x-text="rb.title"></h4>
                                <p class="text-[9px] font-bold text-gray-400 truncate mt-0.5" x-text="rb.author"></p>
                            </a>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <!-- Sticky Bottom Checkout / Borrow Action Bar (Floating Above Mobile Nav) -->
        <div class="fixed bottom-16 sm:bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-gray-100 p-3 sm:p-4 shadow-2xl">
            <div class="max-w-4xl mx-auto flex items-center justify-between gap-4">
                <div class="hidden sm:block">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status Ketersediaan</p>
                    <p class="text-xs font-black" :class="book.status === 'available' ? 'text-emerald-600' : 'text-amber-600'">
                        <span x-text="getStatusText(book.status)"></span>
                        <span class="text-gray-400 font-medium"> (Stok: <strong x-text="book.stock_available"></strong>)</span>
                    </p>
                </div>

                <template x-if="book.user_queue_id">
                    <form :action="`{{ url('/member/queues') }}/${book.user_queue_id}/cancel`" method="POST" class="w-full sm:w-auto flex-1 sm:flex-initial" onsubmit="return confirm('Batalkan booking antrean buku ini?')">
                        @csrf
                        <button 
                            type="submit" 
                            class="w-full sm:px-12 py-3.5 sm:py-4 bg-rose-50 hover:bg-rose-100 text-rose-700 border-2 border-rose-300 rounded-2xl text-xs font-black uppercase tracking-widest shadow-md flex items-center justify-center gap-2 active:scale-98 transition-all"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>
                            <span>BATALKAN ANTREAN</span>
                        </button>
                    </form>
                </template>

                <template x-if="!book.user_queue_id">
                    <form :action="`{{ url('/member/books') }}/${book.id}/queue`" method="POST" class="w-full sm:w-auto flex-1 sm:flex-initial">
                        @csrf
                        <button 
                            type="submit" 
                            class="w-full sm:px-12 py-3.5 sm:py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-3 active:scale-98 transition-all"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M8 6h10"/><path d="M8 10h10"/><path d="M8 14h10"/></svg>
                            <span x-text="book.status === 'available' ? '📌 PINJAM BUKU SEKARANG' : '⏳ MASUK ANTREAN BOOKING'"></span>
                        </button>
                    </form>
                </template>
            </div>
        </div>
    </div>

    <script>
        function ecommerceBookApp(initialBook, initialRelated) {
            return {
                book: initialBook || null,
                relatedBooks: initialRelated || [],
                activeTab: 'info',
                userRating: 5,

                shareProduct() {
                    if (navigator.share) {
                        navigator.share({
                            title: this.book?.title || 'Detail Buku',
                            url: window.location.href
                        }).catch(() => {});
                    } else {
                        navigator.clipboard.writeText(window.location.href);
                        alert("Link buku berhasil disalin!");
                    }
                },

                getStatusClass(status) {
                    const map = {
                        'available': 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                        'borrowed': 'bg-amber-100 text-amber-800 border border-amber-200',
                        'reserved': 'bg-indigo-100 text-indigo-800 border border-indigo-200',
                        'lost': 'bg-red-100 text-red-800 border border-red-200'
                    };
                    return map[status] || 'bg-gray-100 text-gray-700';
                },

                getStatusText(status) {
                    const map = {
                        'available': 'TERSEDIA',
                        'borrowed': 'SEDANG DIPINJAM',
                        'reserved': 'DIBOOKING',
                        'lost': 'HILANG'
                    };
                    return map[status] || (status || 'UNKNOWN').toUpperCase();
                }
            }
        }
    </script>

    <style>
        .animate-fade-in { animation: fadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</x-member-layout>
