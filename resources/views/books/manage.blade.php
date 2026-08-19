<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-lg text-gray-800 leading-tight">{{ $panelTitle }}</h2>
    </x-slot>

    <!-- Wrapper Utama: Mengatur jarak antar elemen secara vertikal -->
    <div class="space-y-6">
        @if (session('success'))
            <!-- Notifikasi Sukses: Muncul setelah aksi simpan/hapus -->
            <div class="px-5 py-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl flex items-center gap-3 shadow-sm animate-fade-in">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span class="text-sm font-bold">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Stats Overview (Optional but adds professionalism) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-700 rounded-2xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-book-copy"><path d="M2 20V4c0-1.1.9-2 2-2h9.414a1 1 0 0 1 .707.293L19.707 7.707A1 1 0 0 1 20 8.414V20c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2Z"/><path d="M6 18h4"/><path d="M6 14h8"/><path d="M15 2v5h5"/></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Koleksi</p>
                    <p class="text-2xl font-black text-gray-900">{{ $books->total() }}</p>
                </div>
            </div>
            <!-- Add more stats if needed -->
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <!-- Table Header / Actions -->
            <div class="p-8 border-b border-gray-50 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-xl font-black text-gray-900 tracking-tight">Daftar Koleksi Buku</h3>
                    <p class="text-xs text-gray-500 mt-1 font-medium">Kelola data buku, kategori, dan cetak label QR.</p>
                </div>
                <button type="button" id="open-add-book-modal-btn" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-2xl shadow-xl shadow-emerald-100 transition-all active:scale-95 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                    TAMBAH BUKU
                </button>
            </div>

            <div class="p-8 overflow-x-auto">
                <!-- Bulk Actions & Filters -->
                <div class="mb-8 flex flex-wrap gap-4 items-center justify-between bg-gray-50 p-4 rounded-3xl border border-gray-100">
                    <form method="POST" action="{{ route($routePrefix . '.books.labels.bulk') }}" id="bulk-label-form" class="flex flex-wrap gap-4 items-center">
                        @csrf
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-2">Ukuran Label:</span>
                            <select name="size" class="bg-white border-gray-200 rounded-xl text-xs font-bold text-gray-700 focus:ring-emerald-500">
                                <option value="80x70" selected>8x7 cm (Standar)</option>
                                <option value="100x50">10x5 cm (Panjang)</option>
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-widest rounded-xl hover:bg-emerald-200 transition-colors flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-printer"><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 9V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v5"/><rect width="12" height="8" x="6" y="14" rx="1"/></svg>
                            Cetak Label
                        </button>
                    </form>

                    <!-- Filter Status Dropdown -->
                    <form method="GET" action="{{ route($routePrefix . '.books.index') }}" class="flex items-center gap-2">
                        @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                        <select name="status" onchange="this.form.submit()" class="bg-white border-gray-200 rounded-xl text-xs font-bold text-gray-700 focus:ring-emerald-500 shadow-sm">
                            <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>-- Semua Status --</option>
                            <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Tersedia</option>
                            <option value="borrowed" {{ request('status') === 'borrowed' ? 'selected' : '' }}>Dipinjam</option>
                            <option value="reserved" {{ request('status') === 'reserved' ? 'selected' : '' }}>Dipesan</option>
                            <option value="lost" {{ request('status') === 'lost' ? 'selected' : '' }}>Hilang</option>
                        </select>
                    </form>
                </div>

                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left">
                            <th class="pb-4 px-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap">Pilih</th>
                            <th class="pb-4 px-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap">Buku</th>
                            <th class="pb-4 px-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap">Katalog</th>
                            <th class="pb-4 px-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap">Status</th>
                            <th class="pb-4 px-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap">Antrean</th>
                            <th class="pb-4 px-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($books as $book)
                            <tr class="group hover:bg-gray-50/50 transition-colors">
                                <td class="py-5 px-2">
                                    <input type="checkbox" name="book_ids[]" value="{{ $book->id }}" form="bulk-label-form" class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                </td>
                                <td class="py-5 px-2">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-16 rounded-xl overflow-hidden shadow-sm bg-gray-100 flex-shrink-0 border border-gray-100 overflow-hidden">
                                            @if($book->cover_image)
                                                <img src="{{ route('books.cover', $book) }}" alt="cover" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-image"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="font-bold text-gray-900 leading-snug">{{ $book->title }}</p>
                                            <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-wider font-medium">{{ $book->author ?? 'Penulis Anonim' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-5 px-2">
                                    <div class="space-y-1">
                                        <p class="text-[11px] font-black text-indigo-600 tracking-wider bg-indigo-50 px-2 py-0.5 rounded-lg inline-block">{{ $book->code }}</p>
                                        <p class="text-[10px] text-gray-400 font-medium block">ISBN: {{ $book->isbn ?? '-' }}</p>
                                    </div>
                                </td>
                                <td class="py-5 px-4">
                                    @php
                                        $statusClass = match($book->status) {
                                            'available' => 'bg-emerald-100 text-emerald-700',
                                            'borrowed' => 'bg-amber-100 text-amber-700',
                                            'reserved' => 'bg-blue-100 text-blue-700',
                                            default => 'bg-red-100 text-red-700',
                                        };
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $statusClass }}">
                                        {{ $book->status }}
                                    </span>
                                </td>
                                <td class="py-5 px-4 text-center">
                                    @if($book->active_queues_count > 0)
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-red-50 text-red-600 text-[10px] font-black border border-red-100 shadow-sm">
                                            {{ $book->active_queues_count }}
                                        </span>
                                    @else
                                        <span class="text-[10px] text-gray-300 font-bold">-</span>
                                    @endif
                                </td>
                                <td class="py-5 px-2 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Detail Riwayat (BARU) -->
                                        <a href="{{ route($routePrefix . '.circulation.book.detail', $book->code) }}" class="w-8 h-8 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition-all shadow-sm" title="Riwayat & Detail">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                                        </a>

                                        <button
                                            type="button"
                                            class="open-edit-book-modal-btn w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-600 rounded-xl hover:bg-emerald-600 hover:text-white transition-all shadow-sm"
                                            title="Edit Buku"
                                            data-update-url="{{ route($routePrefix . '.books.update', $book) }}"
                                            data-title="{{ $book->title }}"
                                            data-author="{{ $book->author }}"
                                            data-isbn="{{ $book->isbn }}"
                                            data-pages="{{ $book->pages }}"
                                            data-category="{{ $book->category }}"
                                            data-rack-code="{{ $book->rack_code }}"
                                            data-label-color="{{ $book->label_color }}"
                                            data-exemplar-no="{{ $book->exemplar_no ?? 1 }}"
                                            data-status="{{ $book->status }}"
                                            data-code="{{ $book->code }}"
                                            data-cover-url="{{ $book->cover_image ? route('books.cover', $book) : '' }}"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                        </button>
                                        
                                        <a href="{{ route($routePrefix . '.books.label', [$book, 'size' => '80x70']) }}" class="w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-600 rounded-xl hover:bg-purple-600 hover:text-white transition-all shadow-sm" title="Cetak Label">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-tag"><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><path d="M7 7h.01"/></svg>
                                        </a>
 
                                        <form method="POST" action="{{ route($routePrefix . '.books.destroy', $book) }}" onsubmit="return confirm('Hapus buku ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 flex items-center justify-center bg-red-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-12 text-center text-gray-400 font-medium">Belum ada data buku yang tersedia.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-8">{{ $books->links() }}</div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Buku (Premium Redesign) -->
    <div id="add-book-modal" class="fixed inset-0 bg-[#064e3b]/60 backdrop-blur-md hidden items-start justify-center z-[100] p-4 overflow-y-auto">
        <div class="bg-white w-full max-w-4xl rounded-[3rem] shadow-2xl overflow-hidden animate-pop-in border border-white/20">
            <!-- Header Modal -->
            <div class="bg-gradient-to-r from-emerald-600 to-teal-700 p-8 text-white relative h-32 flex flex-col justify-end">
                <div class="absolute top-6 right-8 flex gap-2">
                    <button type="button" id="close-add-book-modal-btn" class="p-2.5 rounded-2xl bg-white/10 hover:bg-white/20 transition-all text-white backdrop-blur-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
                <div>
                    <h3 class="text-2xl font-black tracking-tight">Tambah Koleksi Baru</h3>
                    <p class="text-[10px] text-emerald-100/70 font-bold uppercase tracking-[0.2em] mt-1">Skarifta Perpus Management • Input Data Buku</p>
                </div>
            </div>

            <div class="p-10">
                <form method="POST" action="{{ route($routePrefix . '.books.store') }}" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-x-8 gap-y-6">
                        <!-- Left Column: Informasi Utama -->
                        <div class="md:col-span-12 lg:col-span-8 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2.5 ml-1">Judul Lengkap Buku</label>
                                    <input type="text" name="title" value="{{ old('title') }}" placeholder="Contoh: Sang Pemimpi" class="w-full bg-gray-50/50 border-gray-100 rounded-2xl py-3.5 px-5 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm font-bold placeholder:font-normal placeholder:text-gray-300 shadow-sm" required>
                                </div>
                                
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2.5 ml-1">Penulis / Pengarang</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                        </div>
                                        <input type="text" name="author" value="{{ old('author') }}" placeholder="Nama penulis" class="w-full bg-gray-50/50 border-gray-100 rounded-2xl py-3.5 pl-12 pr-5 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm font-bold shadow-sm">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2.5 ml-1">ISBN / ISSN</label>
                                    <input type="text" name="isbn" value="{{ old('isbn') }}" placeholder="XXX-XXXXX-XX-X" class="w-full bg-gray-50/50 border-gray-100 rounded-2xl py-3.5 px-5 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm font-bold shadow-sm">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2.5 ml-1">Jml Halaman</label>
                                    <input type="number" min="1" name="pages" value="{{ old('pages', 1) }}" class="w-full bg-gray-50/50 border-gray-100 rounded-2xl py-3.5 px-5 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm font-bold shadow-sm" required>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2.5 ml-1">Status Inventaris</label>
                                    <select name="status" class="w-full bg-gray-50/50 border-gray-100 rounded-2xl py-3.5 px-5 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm font-black text-emerald-700 shadow-sm uppercase tracking-wide" required>
                                        <option value="available">✓ AVAILABLE (Tersedia)</option>
                                        <option value="borrowed">⚠ BORROWED (Dipinjam)</option>
                                        <option value="reserved">★ RESERVED (Dipesan)</option>
                                        <option value="lost">✖ LOST (Hilang)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Katalog & Cover -->
                        <div class="md:col-span-12 lg:col-span-4 space-y-6">
                            <div class="bg-gray-50/50 rounded-[2rem] p-6 border border-gray-100 space-y-5 shadow-inner">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Klasifikasi Koleksi</label>
                                    <div class="space-y-3">
                                        <select name="category" class="w-full bg-white border-gray-100 rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500/20 transition-all text-[11px] font-bold shadow-sm">
                                            <option value="">-- Kategori --</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->name }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        <select name="rack_code" class="w-full bg-white border-gray-100 rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500/20 transition-all text-[11px] font-bold shadow-sm">
                                            <option value="">-- Lokasi Rak --</option>
                                            @foreach($racks as $rack)
                                                <option value="{{ $rack->code }}">{{ $rack->code }} ({{ $rack->name ?? '-' }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[8px] font-black text-gray-300 uppercase tracking-widest mb-1.5 ml-1">Eksemplar</label>
                                        <input type="number" name="exemplar_no" value="1" class="w-full bg-white border-gray-100 rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500/20 transition-all text-xs font-bold shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-black text-gray-300 uppercase tracking-widest mb-1.5 ml-1">Label Warna</label>
                                        <select name="label_color" class="w-full bg-white border-gray-100 rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500/20 transition-all text-[10px] font-bold shadow-sm">
                                            <option value="">Warna</option>
                                            @foreach($labelColors as $lc)
                                                <option value="{{ $lc->name }}">{{ $lc->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="relative group">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1 text-center">Visual Cover</label>
                                <div id="cover-preview-container" class="w-full aspect-[3/4] bg-white rounded-3xl flex items-center justify-center border-2 border-dashed border-gray-100 overflow-hidden relative shadow-xl shadow-gray-100/50 group-hover:border-emerald-200 transition-all cursor-pointer">
                                    <img id="cover-image-preview" src="#" alt="preview" class="hidden w-full h-full object-cover">
                                    <div id="cover-placeholder-icon" class="text-center p-6">
                                        <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-inner">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                        </div>
                                        <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">Pilih Gambar</p>
                                    </div>
                                    <input id="cover-image-input" type="file" name="cover_image" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-8 border-t border-gray-50">
                        <button type="button" id="cancel-add-book-modal-btn" class="px-8 py-4 text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] hover:text-gray-900 transition-colors">Batal</button>
                        <button type="submit" class="px-10 py-4 bg-emerald-600 text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl shadow-2xl shadow-emerald-200 hover:bg-emerald-700 active:scale-[0.98] transition-all">Simpan Koleksi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Buku (Premium Redesign) -->
    <div id="edit-book-modal" class="fixed inset-0 bg-[#064e3b]/60 backdrop-blur-md hidden items-start justify-center z-[100] p-4 overflow-y-auto">
        <div class="bg-white w-full max-w-4xl rounded-[3rem] shadow-2xl overflow-hidden animate-pop-in border border-white/20">
            <!-- Header Modal Edit -->
            <div class="bg-gradient-to-r from-emerald-700 to-indigo-800 p-8 text-white relative h-32 flex flex-col justify-end">
                <div class="absolute top-6 right-8">
                    <button type="button" id="close-edit-book-modal-btn" class="p-2.5 rounded-2xl bg-white/10 hover:bg-white/20 transition-all text-white backdrop-blur-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
                <div class="flex items-end justify-between">
                    <div>
                        <h3 class="text-2xl font-black tracking-tight">Perbarui Koleksi</h3>
                        <p class="text-[10px] text-emerald-200/70 font-bold uppercase tracking-[0.2em] mt-1">Sistem Pembaruan Katalog Terpusat</p>
                    </div>
                    <div class="bg-white/10 px-4 py-2 rounded-xl backdrop-blur-sm shadow-xl border border-white/10 mb-1">
                         <p id="edit-header-code" class="text-base font-black tracking-widest text-emerald-300">BK000</p>
                    </div>
                </div>
            </div>

            <div class="p-10">
                <form id="edit-book-form" method="POST" action="" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-x-8 gap-y-6">
                        <div class="md:col-span-12 lg:col-span-8 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <div class="md:col-span-3">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2.5 ml-1">Judul Buku</label>
                                    <input id="edit-title" type="text" name="title" class="w-full bg-gray-50/50 border-gray-100 rounded-2xl py-3.5 px-5 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm font-bold shadow-sm" required>
                                </div>
                                <div class="md:col-span-1">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2.5 ml-1">Kode Unik</label>
                                    <input id="edit-code" type="text" class="w-full bg-gray-100 border-none rounded-2xl py-3.5 px-4 text-[11px] font-black tracking-widest text-emerald-700 text-center" readonly>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2.5 ml-1">Penulis</label>
                                    <input id="edit-author" type="text" name="author" class="w-full bg-gray-50/50 border-gray-100 rounded-2xl py-3.5 px-5 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm font-bold shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2.5 ml-1">ISBN / Identifier</label>
                                    <input id="edit-isbn" type="text" name="isbn" class="w-full bg-gray-50/50 border-gray-100 rounded-2xl py-3.5 px-5 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm font-bold shadow-sm">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2.5 ml-1">Halaman</label>
                                    <input id="edit-pages" type="number" name="pages" class="w-full bg-gray-50/50 border-gray-100 rounded-2xl py-3.5 px-5 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm font-bold shadow-sm">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2.5 ml-1">Ubah Status</label>
                                    <select id="edit-status" name="status" class="w-full bg-gray-50/50 border-gray-100 rounded-2xl py-3.5 px-5 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm font-black text-indigo-700 shadow-sm uppercase tracking-wide">
                                        <option value="available">AVAILABLE</option>
                                        <option value="borrowed">BORROWED</option>
                                        <option value="reserved">RESERVED</option>
                                        <option value="lost">LOST</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-12 lg:col-span-4 space-y-6">
                            <div class="bg-gray-50/50 rounded-[2rem] p-6 border border-gray-100 space-y-5 shadow-inner">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Update Klasifikasi</label>
                                    <div class="space-y-3">
                                        <select id="edit-category" name="category" class="w-full bg-white border-gray-100 rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500/20 text-[11px] font-bold shadow-sm">
                                            <option value="">-- Kategori --</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->name }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        <select id="edit-rack-code" name="rack_code" class="w-full bg-white border-gray-100 rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500/20 text-[11px] font-bold shadow-sm">
                                            <option value="">-- Lokasi Rak --</option>
                                            @foreach($racks as $rack)
                                                <option value="{{ $rack->code }}">{{ $rack->code }} ({{ $rack->name ?? '-' }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <input id="edit-exemplar-no" type="number" name="exemplar_no" placeholder="Eksemplar" class="w-full bg-white border-gray-100 rounded-xl py-3 px-4 text-[11px] font-bold shadow-sm">
                                    <select id="edit-label-color" name="label_color" class="w-full bg-white border-gray-100 rounded-xl py-3 px-4 text-[10px] font-bold shadow-sm">
                                        <option value="">Warna</option>
                                        @foreach($labelColors as $lc)
                                            <option value="{{ $lc->name }}">{{ $lc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Cover Preview for Edit -->
                            <div class="relative group">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1 text-center">Ganti Cover</label>
                                <div class="w-full aspect-[3/4] bg-white rounded-3xl flex items-center justify-center border-2 border-dashed border-gray-100 overflow-hidden relative shadow-xl shadow-gray-100/50 group-hover:border-indigo-200 transition-all cursor-pointer">
                                    <img id="edit-cover-preview" src="#" alt="preview" class="hidden w-full h-full object-cover">
                                    <div id="edit-cover-placeholder" class="text-center p-6 text-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                                        <p class="text-[9px] font-black uppercase tracking-widest mt-2">Upload Baru</p>
                                    </div>
                                    <input id="edit-cover-input" type="file" name="cover_image" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-8 border-t border-gray-50">
                        <button type="button" id="cancel-edit-book-modal-btn" class="px-8 py-4 text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] hover:text-gray-900 transition-colors">Batal</button>
                        <button type="submit" class="px-10 py-4 bg-emerald-600 text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl shadow-2xl shadow-emerald-200 hover:bg-emerald-700 active:scale-[0.98] transition-all transform-gpu">Terapkan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const modal = document.getElementById('add-book-modal');
            const editModal = document.getElementById('edit-book-modal');
            const openModalBtn = document.getElementById('open-add-book-modal-btn');
            const closeModalBtn = document.getElementById('close-add-book-modal-btn');
            const cancelModalBtn = document.getElementById('cancel-add-book-modal-btn');
            const editButtons = document.querySelectorAll('.open-edit-book-modal-btn');
            const editForm = document.getElementById('edit-book-form');
            const closeEditModalBtn = document.getElementById('close-edit-book-modal-btn');
            const cancelEditModalBtn = document.getElementById('cancel-edit-book-modal-btn');

            function openModal() {
                modal?.classList.remove('hidden');
                modal?.classList.add('flex');
            }
            function closeModal() {
                modal?.classList.add('hidden');
                modal?.classList.remove('flex');
            }
            function openEditModal() {
                editModal?.classList.remove('hidden');
                editModal?.classList.add('flex');
            }
            function closeEditModal() {
                editModal?.classList.add('hidden');
                editModal?.classList.remove('flex');
            }

            openModalBtn?.addEventListener('click', openModal);
            closeModalBtn?.addEventListener('click', closeModal);
            cancelModalBtn?.addEventListener('click', closeModal);
            closeEditModalBtn?.addEventListener('click', closeEditModal);
            cancelEditModalBtn?.addEventListener('click', closeEditModal);

            editButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    editForm.action = btn.dataset.updateUrl;
                    document.getElementById('edit-title').value = btn.dataset.title;
                    document.getElementById('edit-code').value = btn.dataset.code;
                    document.getElementById('edit-header-code').innerText = btn.dataset.code;
                    document.getElementById('edit-author').value = btn.dataset.author;
                    document.getElementById('edit-isbn').value = btn.dataset.isbn;
                    document.getElementById('edit-pages').value = btn.dataset.pages;
                    document.getElementById('edit-status').value = btn.dataset.status;
                    document.getElementById('edit-exemplar-no').value = btn.dataset.exemplarNo;
                    
                    // New Fields
                    document.getElementById('edit-category').value = btn.dataset.category || '';
                    document.getElementById('edit-rack-code').value = btn.dataset.rackCode || '';
                    document.getElementById('edit-label-color').value = btn.dataset.labelColor || '';

                    // Edit Preview logic
                    const editPreview = document.getElementById('edit-cover-preview');
                    const editPlaceholder = document.getElementById('edit-cover-placeholder');
                    if(btn.dataset.coverUrl) {
                        editPreview.src = btn.dataset.coverUrl;
                        editPreview.classList.remove('hidden');
                        editPlaceholder.classList.add('hidden');
                    } else {
                        editPreview.src = '#';
                        editPreview.classList.add('hidden');
                        editPlaceholder.classList.remove('hidden');
                    }

                    openEditModal();
                });
            });

            // Preview logic Helper
            function setupPreview(inputId, previewId, placeholderId) {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);
                const placeholder = document.getElementById(placeholderId);

                input?.addEventListener('change', (e) => {
                    const file = e.target.files[0];
                    if(file) {
                        const reader = new FileReader();
                        reader.onload = (re) => {
                            preview.src = re.target.result;
                            preview.classList.remove('hidden');
                            placeholder.classList.add('hidden');
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }

            setupPreview('cover-image-input', 'cover-image-preview', 'cover-placeholder-icon');
            setupPreview('edit-cover-input', 'edit-cover-preview', 'edit-cover-placeholder');
        })();
    </script>

    <style>
        @keyframes pop-in {
            0% { transform: scale(0.95); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-pop-in { animation: pop-in 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
    </style>
</x-app-layout>
