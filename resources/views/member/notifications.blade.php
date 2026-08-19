<x-member-layout>
    <div class="px-6 py-6 space-y-8">
        <section>
            <h2 class="text-3xl font-bold text-gray-900 leading-[1.1]">Notifications</h2>
            <p class="text-gray-500 text-sm mt-3 leading-relaxed">Pantau aktivitas antrian dan pengingat peminjaman.</p>
        </section>

        <!-- Notification List -->
        <div class="space-y-4">
            @forelse($notifications as $notification)
                @php
                    $isBookReady = ($notification->data['type'] ?? '') === 'book_ready';
                @endphp
                <div class="bg-white rounded-[2rem] p-5 border border-gray-100 shadow-sm flex gap-4 {{ $notification->read_at ? 'opacity-60' : '' }}">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 {{ $isBookReady ? 'bg-emerald-50 text-emerald-600' : 'bg-indigo-50 text-indigo-600' }}">
                        @if($isBookReady)
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-book-open-check"><path d="M12 21V7"/><path d="m16 12 2 2 4-4"/><path d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3.97 3.97 0 0 1 2 1 3.97 3.97 0 0 1 2-1h1"/><path d="m22 10-4 4-2-2"/></svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="text-sm font-bold text-gray-900 truncate">{{ $notification->data['title'] ?? 'Notifikasi' }}</h4>
                            <span class="text-[10px] text-gray-400 font-medium">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-gray-500 leading-relaxed">{{ $notification->data['message'] ?? '-' }}</p>
                        
                        @if($isBookReady)
                             <div class="mt-4 flex gap-2">
                                <a href="{{ route('member.books.index') }}" class="px-4 py-2 bg-emerald-600 text-white text-[10px] font-bold rounded-xl active:scale-95 transition-all uppercase tracking-widest">
                                    Cek Katalog
                                </a>
                             </div>
                        @endif
                    </div>
                </div>
            @empty
                <!-- Empty State (Modern) -->
                <div class="py-20 flex flex-col items-center text-center">
                    <div class="relative w-32 h-32 mb-8">
                        <div class="absolute inset-0 bg-indigo-50 rounded-full animate-pulse"></div>
                        <div class="relative w-32 h-32 bg-white rounded-full flex items-center justify-center shadow-2xl shadow-indigo-100 border border-indigo-50">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell-off"><path d="M8.7 3A6 6 0 0 1 18 8a21.3 21.3 0 0 0 .6 5"/><path d="M17 17H3s3-2 3-9a4.67 4.67 0 0 1 .3-1.7"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/><path d="m2 2 20 20"/></svg>
                        </div>
                    </div>
                    
                    <h4 class="text-xl font-bold text-gray-900">Belum ada notifikasi</h4>
                    <p class="text-sm text-gray-400 mt-2 max-w-[260px]">
                        Tenang saja, kami akan mengabari jika buku antrianmu sudah siap diambil!
                    </p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="pt-4">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</x-member-layout>
