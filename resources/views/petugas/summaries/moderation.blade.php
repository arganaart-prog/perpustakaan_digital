<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-book-check text-emerald-600"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M18 6h2"/><path d="M18 10h2"/><path d="M18 14h2"/><path d="M8 6h3"/><path d="M8 10h3"/><path d="M8 14h3"/><path d="m9 17 2 2 4-4"/></svg>
            {{ __('Moderasi Rangkuman') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-emerald-100">
                <div class="p-6 text-gray-900">
                    
                    @if(session('success'))
                        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3 animate-fade-in">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-100 bg-gray-50/50">
                                    <th class="px-4 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Siswa</th>
                                    <th class="px-4 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Buku</th>
                                    <th class="px-4 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Tanggal Unggah</th>
                                    <th class="px-4 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingSummaries as $summary)
                                    <tr class="border-b border-gray-50 hover:bg-gray-50/80 transition-colors group">
                                        <td class="px-4 py-4">
                                            <div class="font-bold text-gray-900">{{ $summary->borrow->user->name }}</div>
                                            <div class="text-[10px] text-gray-400 uppercase tracking-wider">{{ $summary->borrow->user->kelas }} - {{ $summary->borrow->user->jurusan }}</div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="font-medium text-gray-700">{{ $summary->borrow->book->title }}</div>
                                            <div class="text-[10px] text-emerald-600 font-bold tracking-widest">{{ $summary->borrow->book->code }}</div>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-500">
                                            {{ $summary->created_at->format('d M Y, H:i') }}
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('summaries.view-file', $summary) }}" target="_blank" class="p-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white transition-all shadow-sm" title="Lihat File">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                                </a>
                                                
                                                <form action="{{ route('petugas.summaries.approve', $summary) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="p-2 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-600 hover:text-white transition-all shadow-sm" title="Setujui">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check"><path d="M20 6 9 17l-5-5"/></svg>
                                                    </button>
                                                </form>

                                                <button 
                                                    onclick="openRejectModal({{ $summary->id }}, '{{ $summary->borrow->user->name }}')"
                                                    class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm" 
                                                    title="Tolak"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-12 text-center text-gray-400 text-sm">
                                            Tidak ada rangkuman yang perlu dimoderasi.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $pendingSummaries->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeRejectModal()"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-[2rem] text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="bg-white px-8 pt-8 pb-6">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 bg-red-50 rounded-2xl flex items-center justify-center text-red-600">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-alert-triangle"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900" id="modal-title">Tolak Rangkuman</h3>
                                <p class="text-xs text-gray-400 font-medium">Siswa: <span id="studentName"></span></p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label for="review_note" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Alasan Penolakan</label>
                                <textarea 
                                    name="review_note" 
                                    id="review_note" 
                                    rows="4" 
                                    required
                                    placeholder="Contoh: Rangkuman terlalu pendek atau tidak relevan dengan isi buku..."
                                    class="w-full rounded-2xl border-gray-100 bg-gray-50 focus:border-emerald-500 focus:ring-emerald-500 transition-all text-sm"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50/50 px-8 py-6 flex flex-col sm:flex-row-reverse gap-3">
                        <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-red-600 text-white rounded-2xl text-sm font-bold shadow-lg shadow-red-100 active:scale-95 transition-all">
                            Konfirmasi Tolak
                        </button>
                        <button type="button" onclick="closeRejectModal()" class="w-full sm:w-auto px-8 py-3 bg-white text-gray-500 rounded-2xl text-sm font-bold border border-gray-100 active:scale-95 transition-all">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openRejectModal(id, name) {
            const modal = document.getElementById('rejectModal');
            const form = document.getElementById('rejectForm');
            const nameSpan = document.getElementById('studentName');
            
            form.action = `/petugas/summaries/${id}/reject`;
            nameSpan.innerText = name;
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.add('flex');
            }, 10);
        }

        function closeRejectModal() {
            const modal = document.getElementById('rejectModal');
            modal.classList.add('hidden');
        }
    </script>
</x-app-layout>
