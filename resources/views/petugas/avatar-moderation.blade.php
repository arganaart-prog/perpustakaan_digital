<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Moderasi Foto Profil Baru
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 space-y-6">

            {{-- Flash Messages --}}
            @if (session('status'))
                <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-semibold">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm font-semibold">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 bg-slate-50 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800 text-lg">Antrean Persetujuan Foto Profil</h3>
                    <p class="text-xs text-gray-500 mt-1">Harap tinjau foto profil baru yang diajukan oleh pengguna perpustakaan sebelum memberikan persetujuan.</p>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse($pendingUsers as $user)
                        <div class="p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                            <!-- User Details -->
                            <div class="space-y-2 flex-1">
                                <div class="flex items-center gap-3">
                                    <h4 class="font-black text-gray-900 text-base">{{ $user->name }}</h4>
                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-700 text-[10px] font-bold uppercase rounded-md tracking-wider">
                                        {{ $user->member_type === 'teacher' ? 'Guru' : ($user->member_type === 'student' ? 'Siswa' : 'Staff') }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 font-medium">Email: <span class="text-gray-800 font-semibold">{{ $user->email }}</span></p>
                                @if($user->kelas)
                                    <p class="text-xs text-gray-500 font-medium">Kelas: <span class="text-gray-800 font-semibold">{{ $user->kelas }} ({{ $user->jurusan }})</span></p>
                                @endif
                                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100/50 mt-2">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Motto Hidup / Bio</span>
                                    <p class="text-xs text-gray-600 italic font-medium">"{{ $user->bio ?: '-' }}"</p>
                                </div>
                            </div>

                            <!-- Photo Comparison -->
                            <div class="flex items-center gap-6 bg-slate-50/50 p-4 rounded-2xl border border-slate-100/30">
                                <!-- Old Photo -->
                                <div class="text-center">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-2">Foto Lama</span>
                                    <div class="w-20 h-20 rounded-full overflow-hidden border-2 border-slate-200 bg-white shadow-xs">
                                        <img src="{{ $user->avatar_url }}" class="w-full h-full object-cover">
                                    </div>
                                </div>

                                <!-- Arrow -->
                                <div class="text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-move-right"><path d="M18 8L22 12L18 16"/><path d="M2 12H22"/></svg>
                                </div>

                                <!-- New Photo Request -->
                                <div class="text-center">
                                    <span class="text-[9px] font-black text-amber-600 uppercase tracking-widest block mb-2">Pengajuan Baru</span>
                                    <div class="w-20 h-20 rounded-full overflow-hidden border-2 border-amber-400 bg-white shadow-md ring-4 ring-amber-50">
                                        <img src="{{ $user->pending_avatar_url }}" class="w-full h-full object-cover">
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2 w-full md:w-auto justify-end">
                                <form action="{{ route('petugas.avatars.reject', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menolak foto profil baru ini?')">
                                    @csrf
                                    <button type="submit" class="px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-black uppercase tracking-wider rounded-xl transition-colors">
                                        Tolak Foto
                                    </button>
                                </form>

                                <form action="{{ route('petugas.avatars.approve', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui foto profil baru ini?')">
                                    @csrf
                                    <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black uppercase tracking-wider rounded-xl transition-all shadow-md active:scale-95">
                                        Setujui Foto
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-gray-400 text-sm font-semibold">
                            Tidak ada pengajuan foto profil baru yang pending saat ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Regulations / Rules Guidance Box -->
            <div class="p-6 bg-blue-50 border border-blue-100 rounded-2xl space-y-3">
                <h4 class="font-black text-blue-900 text-sm">💡 Panduan Persetujuan Foto Profil:</h4>
                <ul class="list-disc pl-5 text-xs text-blue-800 space-y-1.5 font-medium">
                    <li>Pastikan wajah pengguna perpustakaan terlihat dengan jelas pada foto pengajuan baru.</li>
                    <li>Foto **tidak boleh** mengandung unsur SARA (Suku, Agama, Ras, dan Antar-golongan).</li>
                    <li>Foto **tidak boleh** mengandung unsur pornografi, kekerasan, gambar politik praktis, atau konten tidak senonoh lainnya.</li>
                    <li>Jika tidak memenuhi kriteria tersebut, harap segera menolak pengajuan tersebut.</li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
