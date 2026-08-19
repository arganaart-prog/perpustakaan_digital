<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Transkrip Obrolan: {{ $conversation->userOne->name }} & {{ $conversation->userTwo->name }}
            </h2>
            <a href="{{ route('petugas.chats.monitoring.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-gray-700 rounded-xl text-xs font-bold transition-all">
                ← Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold animate-fade-in">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Participants Card -->
            <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col sm:flex-row items-center justify-around gap-4 text-center">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-emerald-200 bg-white">
                        <img src="{{ $conversation->userOne->avatar_url }}" class="w-full h-full object-cover">
                    </div>
                    <div class="text-left">
                        <h4 class="font-black text-sm text-gray-900">{{ $conversation->userOne->name }}</h4>
                        <p class="text-xs text-gray-400 font-bold">{{ $conversation->userOne->kelas ?? '-' }} ({{ $conversation->userOne->jurusan ?? '-' }})</p>
                    </div>
                </div>

                <div class="px-4 py-1.5 bg-slate-100 rounded-full text-xs font-bold text-gray-500 uppercase tracking-widest">
                    💬 Transkrip Pesan
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-emerald-200 bg-white">
                        <img src="{{ $conversation->userTwo->avatar_url }}" class="w-full h-full object-cover">
                    </div>
                    <div class="text-left">
                        <h4 class="font-black text-sm text-gray-900">{{ $conversation->userTwo->name }}</h4>
                        <p class="text-xs text-gray-400 font-bold">{{ $conversation->userTwo->kelas ?? '-' }} ({{ $conversation->userTwo->jurusan ?? '-' }})</p>
                    </div>
                </div>
            </div>

            <!-- Messages Stream -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-sm space-y-4 max-h-[650px] overflow-y-auto">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block pb-2 border-b border-gray-100">
                    Riwayat Pesan ({{ $messages->count() }} Pesan)
                </span>

                <div class="space-y-4">
                    @forelse($messages as $msg)
                        @php
                            $isUserOne = $msg->sender_id === $conversation->user_one_id;
                        @endphp
                        <div class="flex flex-col {{ $isUserOne ? 'items-start' : 'items-end' }}">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[10px] font-bold text-gray-500">{{ $msg->sender->name }}</span>
                                <span class="text-[9px] text-gray-400">{{ $msg->created_at->format('d M Y, H:i') }}</span>
                            </div>

                            <div class="flex items-center gap-2 group">
                                <div class="p-4 rounded-2xl text-xs leading-relaxed max-w-md shadow-2xs {{ $isUserOne ? 'bg-slate-100 text-gray-800' : 'bg-indigo-50 text-indigo-900 border border-indigo-100' }}">
                                    <p class="whitespace-pre-wrap break-words">{{ $msg->message }}</p>
                                </div>

                                <!-- Delete Button for Moderator -->
                                <form method="POST" action="{{ route('petugas.chats.messages.destroy', $msg) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan ini demi moderasi?')" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl text-[10px] font-bold transition-colors" title="Hapus pesan ini">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-xs text-gray-400 py-8 font-medium">Belum ada pesan dalam obrolan ini.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
