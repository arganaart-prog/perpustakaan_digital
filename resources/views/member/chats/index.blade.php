<x-member-layout>
    <div class="pb-36 min-h-screen bg-slate-50/60 p-4 sm:p-6 space-y-6">
        <div class="max-w-4xl mx-auto space-y-6 animate-fade-in">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-black text-gray-900 leading-tight">Pesan & Obrolan Siswa</h1>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">Diskusikan buku dan tugas dengan sesama siswa Skarifta.</p>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-lg border border-indigo-100 shadow-2xs">
                    💬
                </div>
            </div>

            <!-- Search Fellow Students -->
            <div class="bg-white rounded-[2rem] p-4 border border-gray-100 shadow-sm">
                <form method="GET" action="{{ route('member.chats.index') }}" class="flex gap-2">
                    <div class="relative flex-1">
                        <input 
                            type="text" 
                            name="q" 
                            value="{{ $q }}" 
                            placeholder="Cari nama murid atau kelas untuk mulai chat..." 
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 rounded-2xl border-none text-xs font-bold text-gray-800 placeholder-gray-400 focus:ring-2 focus:ring-emerald-500"
                        >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                    <button type="submit" class="px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-black uppercase tracking-wider transition-all shadow-xs active:scale-95">
                        Cari
                    </button>
                </form>

                <!-- Search Results -->
                @if($q !== '')
                    <div class="mt-4 pt-4 border-t border-gray-100 space-y-2">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Hasil Pencarian Siswa:</span>
                        @forelse($students as $student)
                            <a href="{{ route('member.chats.start', $student) }}" class="p-3 bg-slate-50 hover:bg-emerald-50 rounded-2xl flex items-center justify-between transition-colors group">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full overflow-hidden border border-gray-200 bg-white">
                                        <img src="{{ $student->avatar_url }}" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-black text-gray-900 group-hover:text-emerald-700">{{ $student->name }}</h4>
                                        <p class="text-[10px] text-gray-400 font-bold">{{ $student->kelas ?? '-' }} ({{ $student->jurusan ?? '-' }})</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1.5 bg-white group-hover:bg-emerald-600 group-hover:text-white text-gray-700 rounded-xl text-[10px] font-black uppercase tracking-wider border border-gray-200 transition-all">
                                    Chat 💬
                                </span>
                            </a>
                        @empty
                            <p class="text-xs text-gray-400 font-medium py-3 text-center">Tidak ditemukan murid dengan kata kunci "{{ $q }}".</p>
                        @endforelse
                    </div>
                @endif
            </div>

            <!-- Active Conversations List -->
            <div class="bg-white rounded-[2.5rem] p-6 border border-gray-100 shadow-sm space-y-3">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Daftar Obrolan Aktif</span>

                <div class="space-y-2">
                    @forelse($conversations as $conv)
                        @php
                            $other = $conv->getOtherUser(auth()->user());
                            $lastMsg = $conv->latestMessage;
                            $unreadCount = $conv->messages()->where('receiver_id', auth()->id())->where('is_read', false)->count();
                        @endphp
                        <a href="{{ route('member.chats.show', $conv) }}" class="p-4 bg-slate-50/70 hover:bg-slate-100/80 rounded-2xl flex items-center justify-between gap-3 transition-all active:scale-[0.99] border border-slate-100">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <div class="relative shrink-0">
                                    <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-white shadow-xs bg-white">
                                        <img src="{{ $other->avatar_url }}" class="w-full h-full object-cover">
                                    </div>
                                    @if($unreadCount > 0)
                                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-emerald-600 text-white rounded-full text-[9px] font-black flex items-center justify-center border-2 border-white shadow-xs">
                                            {{ $unreadCount }}
                                        </span>
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <h4 class="text-xs font-black text-gray-900 truncate">{{ $other->name }}</h4>
                                    <p class="text-[10px] text-gray-400 font-bold truncate">{{ $other->kelas ?? '-' }} ({{ $other->jurusan ?? 'Murid' }})</p>
                                    @if($lastMsg)
                                        <p class="text-xs {{ $unreadCount > 0 ? 'font-bold text-gray-900' : 'text-gray-500' }} truncate mt-1">
                                            {{ $lastMsg->sender_id === auth()->id() ? 'Kamu: ' : '' }}{{ $lastMsg->message }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                <span class="text-[9px] font-bold text-gray-400 block">
                                    {{ $conv->last_message_at ? $conv->last_message_at->format('H:i') : '' }}
                                </span>
                                @if(!$conv->is_accepted && $conv->starter_id === auth()->id())
                                    <span class="mt-1 px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full text-[8px] font-bold uppercase inline-block">
                                        Menunggu Balasan
                                    </span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="py-12 text-center text-gray-400 text-xs font-bold bg-slate-50/50 rounded-2xl border border-dashed border-gray-200">
                            Belum ada percakapan. Cari murid di atas atau buka profil temanmu untuk memulai chat!
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-member-layout>
