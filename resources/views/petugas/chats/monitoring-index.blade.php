<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Monitoring & Moderasi Chat Siswa
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold animate-fade-in flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 space-y-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-gray-100">
                    <div>
                        <h3 class="font-black text-gray-900 text-base">Seluruh Percakapan Antar Siswa</h3>
                        <p class="text-xs text-gray-500 font-medium">Pantau pesan antar siswa untuk memastikan keamanan, etika, dan lingkungan belajar yang kondusif.</p>
                    </div>

                    <!-- Search -->
                    <form method="GET" action="{{ route('petugas.chats.monitoring.index') }}" class="flex gap-2 w-full sm:w-auto">
                        <input 
                            type="text" 
                            name="q" 
                            value="{{ $q }}" 
                            placeholder="Cari nama murid..." 
                            class="text-xs rounded-xl border-gray-200 focus:ring-emerald-500 focus:border-emerald-500 w-full sm:w-64"
                        >
                        <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-black text-white text-xs font-bold rounded-xl shadow-2xs">
                            Cari
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="py-3 px-4 font-black text-gray-400 uppercase tracking-widest">Siswa 1 (Pemulai)</th>
                                <th class="py-3 px-4 font-black text-gray-400 uppercase tracking-widest">Siswa 2 (Penerima)</th>
                                <th class="py-3 px-4 font-black text-gray-400 uppercase tracking-widest">Pesan Terakhir</th>
                                <th class="py-3 px-4 font-black text-gray-400 uppercase tracking-widest text-center w-24">Total Pesan</th>
                                <th class="py-3 px-4 font-black text-gray-400 uppercase tracking-widest text-center w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($conversations as $conv)
                                @php
                                    $lastMsg = $conv->latestMessage;
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full overflow-hidden border border-gray-200 bg-white shrink-0">
                                                <img src="{{ $conv->userOne->avatar_url }}" class="w-full h-full object-cover">
                                            </div>
                                            <div>
                                                <span class="font-black text-gray-900 block">{{ $conv->userOne->name }}</span>
                                                <span class="text-[10px] text-gray-400 font-bold">{{ $conv->userOne->kelas ?? '-' }} ({{ $conv->userOne->jurusan ?? '-' }})</span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full overflow-hidden border border-gray-200 bg-white shrink-0">
                                                <img src="{{ $conv->userTwo->avatar_url }}" class="w-full h-full object-cover">
                                            </div>
                                            <div>
                                                <span class="font-black text-gray-900 block">{{ $conv->userTwo->name }}</span>
                                                <span class="text-[10px] text-gray-400 font-bold">{{ $conv->userTwo->kelas ?? '-' }} ({{ $conv->userTwo->jurusan ?? '-' }})</span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-4 px-4">
                                        @if($lastMsg)
                                            <p class="text-xs text-gray-700 italic max-w-xs truncate">"{{ $lastMsg->message }}"</p>
                                            <span class="text-[9px] text-gray-400">{{ $conv->last_message_at ? $conv->last_message_at->format('d M Y, H:i') : '' }}</span>
                                        @else
                                            <span class="text-gray-400 italic text-[10px]">Belum ada pesan</span>
                                        @endif
                                    </td>

                                    <td class="py-4 px-4 text-center">
                                        <span class="px-2.5 py-1 bg-slate-100 text-slate-800 rounded-full font-black text-xs">
                                            {{ $conv->messages_count }}
                                        </span>
                                    </td>

                                    <td class="py-4 px-4 text-center">
                                        <a href="{{ route('petugas.chats.monitoring.show', $conv) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-[10px] font-black uppercase tracking-wider shadow-2xs transition-all active:scale-95">
                                            <span>Buka Transkrip 👁️</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-gray-400 font-bold text-xs">
                                        Belum ada data obrolan murid di sistem.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $conversations->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
