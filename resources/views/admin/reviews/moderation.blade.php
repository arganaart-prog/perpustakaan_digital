<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-lg text-gray-800 leading-tight">Moderasi Ulasan Buku</h2>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="px-5 py-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl flex items-center gap-3 shadow-sm animate-fade-in">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span class="text-sm font-bold">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50">
                <h3 class="text-xl font-black text-gray-900 tracking-tight">Antrian Peninjauan Ulasan</h3>
                <p class="text-xs text-gray-500 mt-1 font-medium">Klik setujui untuk menampilkan ulasan member ke katalog publik.</p>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse ($pendingReviews as $review)
                        <div class="bg-gray-50 rounded-[2rem] p-6 border border-gray-100 flex flex-col h-full group hover:bg-white hover:shadow-xl hover:shadow-emerald-900/5 transition-all duration-300">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-xs font-black">
                                    {{ substr($review->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-black text-gray-900 leading-tight">{{ $review->user->name }}</p>
                                    <p class="text-[10px] text-emerald-600 font-bold uppercase tracking-wider">{{ $review->user->roles->first()->name ?? 'Member' }}</p>
                                </div>
                            </div>

                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="flex text-amber-400">
                                        @for ($i = 0; $i < $review->rating; $i++)
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                        @endfor
                                    </div>
                                    <span class="text-[10px] font-bold text-gray-400">—</span>
                                    <p class="text-[10px] font-black text-indigo-600 truncate">{{ $review->book->title }}</p>
                                </div>
                                <div class="bg-white p-4 rounded-2xl border border-gray-100 italic text-sm text-gray-600 leading-relaxed mb-6">
                                    "{{ $review->comment }}"
                                </div>
                            </div>

                            <div class="flex gap-2 pt-4 border-t border-gray-100">
                                <form action="{{ route($routePrefix . '.reviews.approve', $review) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-emerald-100 active:scale-95">Setujui</button>
                                </form>
                                <form action="{{ route($routePrefix . '.reviews.destroy', $review) }}" method="POST" class="flex-shrink-0" onsubmit="return confirm('Hapus ulasan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-12 py-3 bg-red-50 hover:bg-red-600 hover:text-white text-red-600 rounded-xl transition-all active:scale-95 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-20 text-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-dashed border-gray-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-gray-300"><path d="M12 17.8 5.8 21 7 14.1 2 9.3l7-1L12 2l3 6.3 7 1-5 4.8 1.2 6.9-6.2-3.3Z"/><path d="M12 2v15.8"/></svg>
                            </div>
                            <p class="text-sm font-bold text-gray-400">Tidak ada ulasan yang menunggu moderasi.</p>
                        </div>
                    @endforelse
                </div>
                
                <div class="mt-8">
                    {{ $pendingReviews->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
