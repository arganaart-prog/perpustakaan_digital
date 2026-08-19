<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Semua Anggota
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4">
            
            @if (session('status'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded text-green-700 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-red-700 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-white rounded-xl shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Terdaftar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($users as $index => $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    @if ($user->member_type === 'student')
                                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full">Murid</span>
                                    @elseif ($user->member_type === 'teacher')
                                        <span class="px-2 py-1 text-xs bg-purple-100 text-purple-700 rounded-full">Guru</span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusMap = [
                                            'register'      => ['label' => 'Belum OTP',  'class' => 'bg-gray-100 text-gray-600'],
                                            'pending'       => ['label' => 'Pending',     'class' => 'bg-yellow-100 text-yellow-700'],
                                            'approved'      => ['label' => 'Disetujui',   'class' => 'bg-green-100 text-green-700'],
                                            'active'        => ['label' => 'Aktif',       'class' => 'bg-emerald-100 text-emerald-700'],
                                            'rejected'      => ['label' => 'Ditolak',     'class' => 'bg-red-100 text-red-700'],
                                            'suspended'     => ['label' => 'Suspended',   'class' => 'bg-red-200 text-red-800'],
                                        ];
                                        $s = $statusMap[$user->status] ?? ['label' => $user->status, 'class' => 'bg-gray-100 text-gray-600'];
                                    @endphp
                                    <span class="px-2 py-1 text-xs rounded-full {{ $s['class'] }}">{{ $s['label'] }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        @if($user->status === 'suspended')
                                            <form method="POST" action="{{ route('petugas.member.unlock', $user) }}" onsubmit="return confirm('Buka blokir (unlock) akun {{ $user->name }}?')">
                                                @csrf
                                                <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white text-xs px-3 py-1 rounded transition">
                                                    Unlock
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('petugas.users.history', $user) }}" class="bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg transition-all active:scale-95 flex items-center gap-1.5 shadow-sm shadow-emerald-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
                                            Aktivitas
                                        </a>

                                        <form method="POST" action="{{ route('petugas.member.destroy', $user) }}" onsubmit="return confirm('HAPUS PERMANEN member {{ $user->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-400">Belum ada anggota.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
