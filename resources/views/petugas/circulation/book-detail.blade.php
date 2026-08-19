<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Buku {{ $book->code }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 space-y-6">
            @if (session('success'))
                <div class="px-4 py-3 bg-green-100 border border-green-300 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-xl shadow p-5">
                <div class="flex gap-4">
                    @if($book->cover_image)
                        <img src="{{ asset('storage/' . $book->cover_image) }}" class="h-36 w-24 object-cover rounded" alt="cover">
                    @endif
                    <div class="space-y-1">
                        <h3 class="font-bold text-xl">{{ $book->title }}</h3>
                        <p class="text-sm text-gray-600">Kode: {{ $book->code }} | ISBN: {{ $book->isbn ?: '-' }}</p>
                        <p class="text-sm text-gray-600">Status Buku: <span class="font-semibold uppercase">{{ $book->status }}</span></p>
                        <p class="text-sm text-gray-600">Jumlah buku AVAILABLE di perpustakaan: <span class="font-semibold">{{ $availableCount }}</span></p>
                    </div>
                </div>
            </div>

            <!-- TABEL ANTREAN AKTIF (BARU) -->
            <div class="bg-white rounded-xl shadow p-5 overflow-x-auto">
                <h3 class="font-semibold text-lg mb-3">Daftar Pengantre Aktif</h3>
                <table class="w-full text-sm border border-gray-200">
                    <thead class="bg-indigo-50 text-indigo-700">
                        <tr>
                            <th class="px-3 py-2 text-left w-16">Urutan</th>
                            <th class="px-3 py-2 text-left">Member</th>
                            <th class="px-3 py-2 text-left">Status Antrean</th>
                            <th class="px-3 py-2 text-left">Tanggal Booking</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($activeQueues as $q)
                            <tr class="{{ $q->status === 'ready' || $q->status === 'called' ? 'bg-emerald-50' : '' }}">
                                <td class="px-3 py-2 font-bold">{{ $loop->iteration }}</td>
                                <td class="px-3 py-2">
                                    <div class="font-semibold">{{ $q->user->name }}</div>
                                    <div class="text-[10px] text-gray-500 uppercase">{{ $q->user->member_id ?? '-' }}</div>
                                </td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $q->status === 'ready' || $q->status === 'called' ? 'bg-emerald-200 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $q->status }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-gray-500">{{ $q->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-8 text-center text-gray-400 font-medium italic">Tidak ada pengantre aktif saat ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <p class="mt-3 text-[10px] text-gray-400 italic font-medium">* Antrean diurutkan berdasarkan First-Come-First-Serve (FIFO).</p>
            </div>

            <div class="bg-white rounded-xl shadow p-5 overflow-x-auto">
                <h3 class="font-semibold text-lg mb-3">Riwayat Peminjaman</h3>
                <table class="w-full text-sm border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2 text-left">Peminjam</th>
                            <th class="px-3 py-2 text-left">Pinjam</th>
                            <th class="px-3 py-2 text-left">Deadline</th>
                            <th class="px-3 py-2 text-left">Kembali</th>
                            <th class="px-3 py-2 text-left">Status</th>
                            <th class="px-3 py-2 text-left">Denda</th>
                            <th class="px-3 py-2 text-left">Rangkuman</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($history as $item)
                            <tr>
                                <td class="px-3 py-2">{{ $item->user->name }}</td>
                                <td class="px-3 py-2">{{ $item->borrow_date->format('d M Y H:i') }}</td>
                                <td class="px-3 py-2">{{ $item->due_date->format('d M Y H:i') }}</td>
                                <td class="px-3 py-2">{{ $item->return_date ? $item->return_date->format('d M Y H:i') : '-' }}</td>
                                <td class="px-3 py-2 uppercase">{{ $item->status }}</td>
                                <td class="px-3 py-2">Rp {{ number_format($item->fine, 0, ',', '.') }}</td>
                                <td class="px-3 py-2">
                                    @if($item->summary)
                                        <div>upload: {{ $item->summary->status }}</div>
                                        @if($item->summary->status === 'pending')
                                            <form method="POST" action="{{ route('petugas.summaries.approve', $item->summary) }}" class="mt-1">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 bg-indigo-600 text-white text-xs rounded">Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('petugas.summaries.reject', $item->summary) }}" class="mt-1">
                                                @csrf
                                                <input type="text" name="review_note" placeholder="Alasan reject" class="border-gray-300 rounded text-xs px-2 py-1" required>
                                                <button type="submit" class="px-2 py-1 bg-red-600 text-white text-xs rounded">Reject</button>
                                            </form>
                                        @elseif($item->summary->status === 'rejected')
                                            <div class="text-xs text-red-700">alasan: {{ $item->summary->review_note ?: '-' }}</div>
                                        @endif
                                    @else
                                        belum upload
                                    @endif

                                    @if($item->fine > 0)
                                        <div class="mt-2">
                                            @if($item->fine_paid_at)
                                                <span class="text-xs text-green-700 font-semibold">Denda lunas ({{ $item->fine_paid_at->format('d M Y H:i') }})</span>
                                            @else
                                                <form method="POST" action="{{ route('petugas.borrows.pay-fine', $item) }}">
                                                    @csrf
                                                    <button type="submit" class="px-2 py-1 bg-emerald-600 text-white text-xs rounded">Pelunasan Denda</button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-3 py-4 text-center text-gray-400">Belum ada riwayat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $history->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
