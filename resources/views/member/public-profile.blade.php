<x-member-layout>
    <div class="pb-36 min-h-screen bg-slate-50/60 p-4 sm:p-6">
        <div class="max-w-4xl mx-auto space-y-6 animate-fade-in">
            <!-- Back Navigation Bar -->
            <div class="flex items-center justify-between">
                <button onclick="window.history.back()" class="px-4 py-2 bg-white hover:bg-slate-100 text-gray-700 rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-xs border border-gray-100 flex items-center gap-2 active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    <span>Kembali</span>
                </button>
            </div>

            <!-- Public Profile Card (Photo, Name, Bio, Social Media) -->
            <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden p-6 sm:p-10 text-center relative">
                <!-- Decorative Background Pattern -->
                <div class="absolute top-0 left-0 right-0 h-32 bg-gradient-to-r from-emerald-600/10 via-teal-600/10 to-emerald-600/10"></div>
                
                <!-- Avatar Section -->
                <div class="relative inline-block mt-4 mb-4 z-10">
                    <div class="w-28 h-28 rounded-full border-4 border-white overflow-hidden shadow-lg mx-auto bg-white">
                        <img src="{{ $user->avatar_url }}" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute bottom-0 right-2 w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center border-2 border-white shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                </div>

                <!-- Name & Meta Info -->
                <h1 class="text-2xl font-black text-gray-900 leading-tight tracking-tight">{{ $user->name }}</h1>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1.5">
                    @if($user->member_type === 'teacher')
                        Guru / Pengajar
                    @elseif($user->member_type === 'student')
                        Siswa (Kelas {{ $user->kelas ?? '-' }} - {{ $user->jurusan ?? 'Umum' }})
                    @else
                        Staff Perpustakaan
                    @endif
                </p>

                <!-- Bio / Quotes Block (Clean & No overlap) -->
                <div class="mt-6 p-5 bg-slate-50/80 rounded-3xl border border-slate-100 max-w-xl mx-auto text-center">
                    <span class="text-[9px] font-black text-emerald-600 uppercase tracking-widest block mb-1.5">✦ Bio & Motto Hidup ✦</span>
                    <p class="text-xs text-gray-700 italic leading-relaxed font-medium">
                        "{{ $user->bio ?: 'Belum menuliskan bio, quote, atau motivasi hidup.' }}"
                    </p>
                </div>

                @if($user->id !== auth()->id() && $user->hasRole('member'))
                    <div class="mt-5">
                        <a href="{{ route('member.chats.start', $user) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-black uppercase tracking-wider shadow-lg shadow-emerald-200 transition-all active:scale-95">
                            <span>Kirim Pesan Chat 💬</span>
                        </a>
                    </div>
                @endif

                <!-- Social Media Badges (Authentic Official SVG Logos) -->
                @php
                    $links = is_array($user->social_links) ? $user->social_links : [];
                @endphp
                @if(count($links) > 0)
                    <div class="mt-8 border-t border-gray-100 pt-6">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-4">Media Sosial</span>
                        
                        <div class="flex flex-wrap justify-center gap-3">
                            @foreach($links as $link)
                                @php
                                    $platform = strtolower($link['platform'] ?? '');
                                    $val = $link['value'] ?? '';
                                    $href = $val;
                                    if (!Str::startsWith($val, 'http')) {
                                        $cleanVal = ltrim($val, '@');
                                        $href = match($platform) {
                                            'instagram' => "https://instagram.com/{$cleanVal}",
                                            'tiktok'    => "https://tiktok.com/@{$cleanVal}",
                                            'facebook'  => "https://facebook.com/{$cleanVal}",
                                            'threads'   => "https://threads.net/@{$cleanVal}",
                                            'linkedin'  => "https://linkedin.com/in/{$cleanVal}",
                                            'x'         => "https://x.com/{$cleanVal}",
                                            default     => $val,
                                        };
                                    }
                                @endphp

                                <a href="{{ $href }}" target="_blank" class="px-4 py-2.5 rounded-2xl bg-slate-50 hover:bg-slate-100 text-gray-800 text-xs font-black flex items-center gap-2.5 border border-slate-100 shadow-2xs transition-all active:scale-95 group">
                                    @switch($platform)
                                        @case('instagram')
                                            <!-- Official Instagram Logo -->
                                            <div class="w-5 h-5 rounded-lg bg-gradient-to-tr from-amber-500 via-rose-500 to-purple-600 text-white flex items-center justify-center p-0.5 shrink-0 shadow-2xs">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                            </div>
                                            <span>Instagram</span>
                                            @break
                                        @case('tiktok')
                                            <!-- Official TikTok Logo -->
                                            <div class="w-5 h-5 rounded-lg bg-black text-white flex items-center justify-center p-0.5 shrink-0 shadow-2xs">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                                            </div>
                                            <span>TikTok</span>
                                            @break
                                        @case('facebook')
                                            <!-- Official Facebook Logo -->
                                            <div class="w-5 h-5 rounded-lg bg-[#1877F2] text-white flex items-center justify-center p-0.5 shrink-0 shadow-2xs">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                            </div>
                                            <span>Facebook</span>
                                            @break
                                        @case('threads')
                                            <!-- Official Threads Logo -->
                                            <div class="w-5 h-5 rounded-lg bg-black text-white flex items-center justify-center p-0.5 shrink-0 shadow-2xs">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12.186 24C5.464 24 0 18.536 0 11.814 0 5.092 5.464 0 12.186 0c6.643 0 11.968 5.244 12.094 11.886v.228c0 .606-.492 1.098-1.098 1.098-.606 0-1.098-.492-1.098-1.098v-.228C21.968 6.45 17.58 2.196 12.186 2.196 6.674 2.196 2.196 6.674 2.196 12.186c0 5.512 4.478 9.99 9.99 9.99 4.316 0 8.012-2.736 9.388-6.696.198-.574.828-.88 1.402-.682.574.198.88.828.682 1.402C22.012 20.944 17.476 24 12.186 24zm4.18-11.872c0 3.326-2.146 5.372-5.068 5.372-2.482 0-4.492-1.748-4.492-4.248 0-2.616 2.146-4.248 5.068-4.248.884 0 1.768.164 2.584.478v-.478c0-1.684-1.12-2.684-2.828-2.684-1.228 0-2.312.56-2.944 1.492-.34.502-1.026.634-1.528.294-.502-.34-.634-1.026-.294-1.528 1.01-1.494 2.766-2.454 4.766-2.454 2.898 0 5.024 1.834 5.024 4.88v3.618h-.288zm-2.194.272c-.7-.306-1.464-.476-2.288-.476-1.758 0-2.874.966-2.874 2.052 0 1.186.994 2.052 2.294 2.052 1.636 0 2.868-1.206 2.868-3.082v-.546z"/></svg>
                                            </div>
                                            <span>Threads</span>
                                            @break
                                        @case('linkedin')
                                            <!-- Official LinkedIn Logo -->
                                            <div class="w-5 h-5 rounded-lg bg-[#0A66C2] text-white flex items-center justify-center p-0.5 shrink-0 shadow-2xs">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                                            </div>
                                            <span>LinkedIn</span>
                                            @break
                                        @case('x')
                                            <!-- Official X Logo -->
                                            <div class="w-5 h-5 rounded-lg bg-black text-white flex items-center justify-center p-0.5 shrink-0 shadow-2xs">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                            </div>
                                            <span>X</span>
                                            @break
                                        @default
                                            <div class="w-5 h-5 rounded-lg bg-emerald-600 text-white flex items-center justify-center p-0.5 shrink-0 shadow-2xs">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
                                            </div>
                                            <span>{{ ucfirst($platform) }}</span>
                                    @endswitch
                                    <span class="text-[10px] text-gray-400 group-hover:text-gray-600 font-medium">({{ Str::limit($val, 15) }})</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Tab Navigation (Riwayat Peminjaman & Riwayat Ulasan) -->
            <div x-data="{ tab: 'borrows' }" class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden p-6 md:p-8">
                <!-- Segmented Control Pill Tabs -->
                <div class="flex bg-slate-100/80 p-1.5 rounded-2xl mb-6">
                    <button 
                        @click="tab = 'borrows'" 
                        :class="tab === 'borrows' ? 'bg-white text-emerald-700 font-black shadow-xs' : 'text-gray-500 font-bold hover:text-gray-800'" 
                        class="flex-1 py-3 text-xs uppercase tracking-wider rounded-xl transition-all flex items-center justify-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/></svg>
                        <span>Riwayat Peminjaman ({{ $borrows->count() }})</span>
                    </button>
                    <button 
                        @click="tab = 'reviews'" 
                        :class="tab === 'reviews' ? 'bg-white text-emerald-700 font-black shadow-xs' : 'text-gray-500 font-bold hover:text-gray-800'" 
                        class="flex-1 py-3 text-xs uppercase tracking-wider rounded-xl transition-all flex items-center justify-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <span>Riwayat Ulasan ({{ $reviews->count() }})</span>
                    </button>
                </div>

                <!-- 1. Riwayat Peminjaman -->
                <div x-show="tab === 'borrows'" class="space-y-4 animate-fade-in">
                    @forelse($borrows as $borrow)
                        <div class="py-4 border-b border-slate-100 last:border-0 flex gap-4 items-center">
                            <div class="w-12 h-16 rounded-xl overflow-hidden bg-slate-100 border border-slate-100 shrink-0 shadow-2xs">
                                <img src="{{ $borrow->book->cover_image ? route('books.cover', $borrow->book) : 'https://ui-avatars.com/api/?name=' . urlencode($borrow->book->title) . '&background=f0fdf4&color=15803d&size=256' }}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs font-black text-gray-900 truncate">{{ $borrow->book->title }}</h4>
                                <p class="text-[10px] text-gray-400 font-bold truncate mt-0.5">{{ $borrow->book->author }}</p>
                                <p class="text-[9px] text-gray-400 mt-1">Pinjam: <span class="font-bold text-gray-700">{{ $borrow->borrow_date ? $borrow->borrow_date->format('d M Y') : '-' }}</span></p>
                            </div>
                            <div class="shrink-0 text-right">
                                @php
                                    $statusClass = match($borrow->status) {
                                        'active' => 'bg-amber-100 text-amber-800 border border-amber-200',
                                        'returned' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                        'late' => 'bg-red-100 text-red-800 border border-red-200',
                                        'lost' => 'bg-slate-100 text-slate-800 border border-slate-200',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                    $statusText = match($borrow->status) {
                                        'active' => 'DIPINJAM',
                                        'returned' => 'DIKEMBALIKAN',
                                        'late' => 'TERLAMBAT',
                                        'lost' => 'HILANG',
                                        default => strtoupper($borrow->status),
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[8px] font-black uppercase tracking-wider {{ $statusClass }}">{{ $statusText }}</span>
                                @if($borrow->return_date)
                                    <p class="text-[8px] text-gray-400 mt-1.5">Kembali: <span class="font-bold text-gray-500">{{ $borrow->return_date->format('d M Y') }}</span></p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-gray-400 text-xs font-bold bg-slate-50/50 rounded-2xl border border-dashed border-gray-200">
                            Belum ada riwayat peminjaman buku.
                        </div>
                    @endforelse
                </div>

                <!-- 2. Riwayat Ulasan Buku -->
                <div x-show="tab === 'reviews'" class="space-y-4 animate-fade-in" style="display: none;">
                    @forelse($reviews as $review)
                        <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-100 space-y-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-xs font-black text-gray-900">{{ $review->book->title }}</h4>
                                    <p class="text-[10px] text-gray-400 font-bold mt-0.5">{{ $review->book->author }}</p>
                                </div>
                                <div class="flex items-center text-amber-400 text-xs font-black">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span>{{ $i <= $review->rating ? '★' : '☆' }}</span>
                                    @endfor
                                    <span class="ml-1 text-[10px] text-gray-500 font-bold">({{ $review->rating }}/5)</span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-700 italic leading-relaxed pt-1">
                                "{{ $review->comment }}"
                            </p>
                            <span class="text-[8px] text-gray-400 font-bold block pt-1">
                                Diulas pada: {{ $review->created_at ? $review->created_at->format('d M Y, H:i') : '-' }}
                            </span>
                        </div>
                    @empty
                        <div class="py-12 text-center text-gray-400 text-xs font-bold bg-slate-50/50 rounded-2xl border border-dashed border-gray-200">
                            Belum ada ulasan buku yang ditulis oleh pengguna ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <style>
        .animate-fade-in { animation: fadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</x-member-layout>
