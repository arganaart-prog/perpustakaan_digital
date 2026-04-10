<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mode Pengembalian</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 space-y-6">
            @if (session('success'))
                <div class="px-4 py-3 bg-green-100 border border-green-300 text-green-800 rounded">{{ session('success') }}</div>
            @endif
            @error('queue')
                <div class="px-4 py-3 bg-red-100 border border-red-300 text-red-800 rounded">{{ $message }}</div>
            @enderror

            <div class="bg-white rounded-xl shadow p-5">
                <h3 class="font-semibold text-lg text-gray-900 mb-3">Scan QR Buku</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border rounded p-3">
                        <video id="camera-preview" class="w-full max-h-64 bg-black rounded object-cover" autoplay muted playsinline></video>
                        <div id="html5qr-reader" class="w-full max-h-64 bg-black rounded overflow-hidden hidden"></div>
                        <div id="html5qr-file-host" class="hidden" aria-hidden="true"></div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <button type="button" id="start-scan-btn" class="px-3 py-2 bg-gray-800 text-white text-xs rounded">Mulai Scan</button>
                            <button type="button" id="stop-scan-btn" class="px-3 py-2 bg-gray-500 text-white text-xs rounded">Stop</button>
                            <button type="button" id="qr-photo-btn" class="px-3 py-2 bg-teal-700 text-white text-xs rounded">Scan dari foto</button>
                        </div>
                        <input type="file" id="qr-image-input" accept="image/*" capture="environment" class="hidden">
                        <p class="text-xs text-amber-800 bg-amber-50 border border-amber-200 rounded px-2 py-1.5 mt-2">
                            Akses lewat <code class="text-[11px]">http://IP-lokal</code> memerlukan <strong>HTTPS</strong> atau konfigurasi manual.<br>
                            <strong>Ingin pakai kamera?</strong> Buka <code class="text-[11px] select-all">chrome://flags/#unsafely-treat-insecure-origin-as-secure</code>, masukkan <code class="text-[11px] select-all">http://192.168.1.24:8000</code>, pilih <strong>Enabled</strong>, lalu restart Chrome.
                        </p>
                    </div>
                    <div class="border rounded p-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Input kode manual</label>
                        <div class="flex gap-2">
                            <input type="text" id="manual-book-code" placeholder="BK001" class="w-full border-gray-300 rounded-md">
                            <button type="button" id="manual-search-btn" class="px-4 py-2 bg-indigo-600 text-white rounded">Cari</button>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Scan akan langsung memunculkan popup detail pengembalian.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="bg-white rounded-xl shadow p-5">
                    <h3 class="font-semibold text-gray-900 mb-3">Antrian READY</h3>
                    <div class="space-y-3">
                        @forelse($readyQueues as $queue)
                            <div class="border rounded p-3">
                                <p class="font-semibold">{{ $queue->user->name }} - {{ $queue->book->title }}</p>
                                <p class="text-xs text-gray-600">Kode: {{ $queue->book->code }} | Ready: {{ optional($queue->ready_at)->format('d-m-Y H:i') ?: '-' }}</p>
                                <div class="mt-2 flex gap-2">
                                    <form method="POST" action="{{ route('petugas.queues.call', $queue) }}">
                                        @csrf
                                        <button type="submit" class="px-3 py-2 text-xs rounded bg-blue-600 text-white">Panggil</button>
                                    </form>
                                    <form method="POST" action="{{ route('petugas.queues.complete', $queue) }}">
                                        @csrf
                                        <button type="submit" class="px-3 py-2 text-xs rounded bg-gray-600 text-white">Tandai Hadir</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Belum ada antrian siap dipanggil.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow p-5">
                    <h3 class="font-semibold text-gray-900 mb-3">Antrian CALLED</h3>
                    <div class="space-y-3">
                        @forelse($calledQueues as $queue)
                            <div class="border rounded p-3">
                                <p class="font-semibold">{{ $queue->user->name }} - {{ $queue->book->title }}</p>
                                <p class="text-xs text-gray-600">Deadline: {{ optional($queue->deadline)->format('d-m-Y H:i') ?: '-' }}</p>
                                <p class="text-xs text-gray-600">Notif: {{ $queue->notified_at ? 'Sudah dikirim' : 'Belum dikirim' }}</p>
                                <div class="mt-2 flex gap-2">
                                    <form method="POST" action="{{ route('petugas.queues.notify', $queue) }}">
                                        @csrf
                                        <button type="submit" class="px-3 py-2 text-xs rounded bg-emerald-600 text-white">Kirim WA + Email</button>
                                    </form>
                                    <form method="POST" action="{{ route('petugas.queues.complete', $queue) }}">
                                        @csrf
                                        <button type="submit" class="px-3 py-2 text-xs rounded bg-gray-600 text-white">Tandai Hadir</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Belum ada antrian yang dipanggil.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="return-modal" class="fixed inset-0 bg-black/50 hidden items-start justify-center z-50 p-4 overflow-y-auto">
        <div class="bg-white w-full max-w-4xl rounded-xl shadow-xl my-4">
            <div class="p-5 border-b flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Detail Buku & Pengembalian</h3>
                <button id="close-return-modal" type="button" class="text-gray-500 hover:text-gray-700">✕</button>
            </div>
            <div class="p-5 space-y-5">
                <div id="return-book-card" class="border rounded p-4 hidden">
                    <div class="flex gap-4">
                        <img id="return-book-cover" src="" alt="cover" class="h-28 w-20 object-cover rounded border border-gray-200 hidden" style="width:80px;height:112px;object-fit:cover;">
                        <div>
                            <p class="font-bold text-lg" id="return-book-title">-</p>
                            <p class="text-sm text-gray-600">Kode: <span id="return-book-code-text">-</span></p>
                            <p class="text-sm text-gray-600">Status:
                                <span id="return-book-status" class="font-semibold inline-block px-2 py-0.5 rounded text-xs">-</span>
                            </p>
                            <p class="text-sm text-gray-600">Kategori: <span id="return-book-category">-</span> | Rak: <span id="return-book-rack">-</span></p>
                            <p class="text-sm text-gray-600">Penulis: <span id="return-book-author">-</span> | Hal: <span id="return-book-pages">-</span></p>
                        </div>
                    </div>
                </div>

                <div id="return-borrow-card" class="border rounded p-4 hidden">
                    <p class="font-semibold text-gray-900 mb-2">Detail Peminjam Aktif</p>
                    <div id="return-borrow-content" class="text-sm text-gray-700 space-y-1"></div>
                </div>

                <div id="return-action-bar" class="hidden border-2 border-blue-300 bg-blue-50 rounded p-4">
                    <form method="POST" action="{{ route('petugas.circulation.return.store') }}">
                        @csrf
                        <input type="hidden" name="book_code" id="return-submit-book-code">
                        <button
                            type="submit"
                            id="confirm-return-btn"
                            class="inline-flex items-center justify-center px-5 py-2.5 text-white rounded text-sm font-semibold border shadow-sm"
                            style="display:inline-flex;min-width:220px;background:#16a34a;border-color:#15803d;cursor:pointer;opacity:1;"
                        >
                            Kembalikan Buku
                        </button>
                    </form>
                    @error('book_code')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js" crossorigin="anonymous"></script>
    <script>
        (function () {
            const returnScanBase = @json(url('/petugas/circulation/return/scan'));
            const video = document.getElementById('camera-preview');
            const html5Region = document.getElementById('html5qr-reader');
            const startBtn = document.getElementById('start-scan-btn');
            const stopBtn = document.getElementById('stop-scan-btn');
            const qrPhotoBtn = document.getElementById('qr-photo-btn');
            const qrImageInput = document.getElementById('qr-image-input');
            const manualInput = document.getElementById('manual-book-code');
            const manualSearchBtn = document.getElementById('manual-search-btn');
            const modal = document.getElementById('return-modal');
            const closeModalBtn = document.getElementById('close-return-modal');
            const returnBookCard = document.getElementById('return-book-card');
            const returnBorrowCard = document.getElementById('return-borrow-card');
            const returnBorrowContent = document.getElementById('return-borrow-content');
            const returnActionBar = document.getElementById('return-action-bar');
            const confirmReturnBtn = document.getElementById('confirm-return-btn');
            const returnSubmitBookCode = document.getElementById('return-submit-book-code');
            const returnBookCover = document.getElementById('return-book-cover');
            const returnBookTitle = document.getElementById('return-book-title');
            const returnBookCodeText = document.getElementById('return-book-code-text');
            const returnBookStatus = document.getElementById('return-book-status');
            const returnBookCategory = document.getElementById('return-book-category');
            const returnBookRack = document.getElementById('return-book-rack');
            const returnBookAuthor = document.getElementById('return-book-author');
            const returnBookPages = document.getElementById('return-book-pages');
            let stream = null;
            let detector = null;
            let timer = null;
            let html5Scanner = null;
            let scanLocked = false;

            function extractCode(raw) {
                if (!raw) return '';
                const match = raw.match(/\/circulation\/book\/([^/?#]+)/i);
                if (match && match[1]) return decodeURIComponent(match[1]).toUpperCase();
                const simpleCode = raw.trim().toUpperCase();
                if (/^[A-Z0-9-]{3,30}$/.test(simpleCode)) return simpleCode;
                return '';
            }

            function setStatusBadge(status) {
                const s = String(status || '').toUpperCase();
                returnBookStatus.classList.remove(
                    'bg-green-100', 'text-green-700',
                    'bg-red-100', 'text-red-700',
                    'bg-yellow-100', 'text-yellow-700',
                    'bg-gray-100', 'text-gray-700'
                );

                if (s === 'AVAILABLE') {
                    returnBookStatus.classList.add('bg-green-100', 'text-green-700');
                } else if (s === 'BORROWED' || s === 'LOST') {
                    returnBookStatus.classList.add('bg-red-100', 'text-red-700');
                } else if (s === 'RESERVED') {
                    returnBookStatus.classList.add('bg-yellow-100', 'text-yellow-700');
                } else {
                    returnBookStatus.classList.add('bg-gray-100', 'text-gray-700');
                }
            }

            function setReturnButtonState(canReturn) {
                confirmReturnBtn.disabled = !canReturn;
                confirmReturnBtn.style.display = 'inline-flex';
                confirmReturnBtn.style.visibility = 'visible';
                confirmReturnBtn.style.opacity = '1';

                if (canReturn) {
                    confirmReturnBtn.textContent = 'Kembalikan Buku';
                    confirmReturnBtn.style.background = '#16a34a';
                    confirmReturnBtn.style.borderColor = '#15803d';
                    confirmReturnBtn.style.cursor = 'pointer';
                } else {
                    confirmReturnBtn.textContent = 'Tidak ada pinjaman aktif';
                    confirmReturnBtn.style.background = '#9ca3af';
                    confirmReturnBtn.style.borderColor = '#6b7280';
                    confirmReturnBtn.style.cursor = 'not-allowed';
                }
            }

            async function loadReturnByCode(code) {
                const response = await fetch(`${returnScanBase}/${encodeURIComponent(code)}`);
                if (!response.ok) return alert('Buku tidak ditemukan');
                const payload = await response.json();
                const book = payload.book;
                const borrow = payload.borrow;

                returnBookTitle.textContent = book.title || '-';
                returnBookCodeText.textContent = book.code || '-';
                returnBookStatus.textContent = book.status || '-';
                setStatusBadge(book.status || '-');
                returnBookCategory.textContent = book.category || '-';
                returnBookRack.textContent = book.rack_code || '-';
                returnBookAuthor.textContent = book.author || '-';
                returnBookPages.textContent = book.pages || '-';
                returnSubmitBookCode.value = book.code || '';

                if (book.cover_url) {
                    returnBookCover.src = book.cover_url;
                    returnBookCover.classList.remove('hidden');
                } else {
                    returnBookCover.classList.add('hidden');
                    returnBookCover.src = '';
                }

                returnBookCard.classList.remove('hidden');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';

                if (!borrow) {
                    returnBorrowCard.classList.remove('hidden');
                    returnBorrowContent.innerHTML = '<div class="text-sm text-gray-600">Tidak ada transaksi pinjam aktif untuk buku ini.</div>';
                    returnActionBar.classList.remove('hidden');
                    setReturnButtonState(false);
                    return;
                }

                const summaryText = borrow.summary && borrow.summary.uploaded
                    ? `sudah upload (${borrow.summary.status || 'pending'})`
                    : 'belum upload';

                returnBorrowCard.classList.remove('hidden');
                returnBorrowContent.innerHTML = `
                    <div><strong>Peminjam:</strong> ${borrow.borrower.name || '-'} (ID: ${borrow.borrower.id || '-'})</div>
                    <div><strong>Kelas/Jurusan:</strong> ${borrow.borrower.kelas || '-'} / ${borrow.borrower.jurusan || '-'}</div>
                    <div><strong>Tanggal pinjam:</strong> ${borrow.borrow_date || '-'}</div>
                    <div><strong>Deadline:</strong> ${borrow.due_date || '-'}</div>
                    <div><strong>Status waktu:</strong> ${borrow.time_status}</div>
                    <div><strong>Keterlambatan:</strong> ${borrow.late_days} hari</div>
                    <div><strong>Estimasi denda:</strong> Rp ${Number(borrow.fine_preview || 0).toLocaleString('id-ID')}</div>
                    <div><strong>Rangkuman:</strong> ${summaryText}</div>
                `;

                returnActionBar.classList.remove('hidden');
                setReturnButtonState(true);
            }

            async function tick() {
                if (!detector || !video || video.readyState < 2 || scanLocked) return;
                try {
                    const barcodes = await detector.detect(video);
                    if (barcodes.length > 0) {
                        const val = barcodes[0].rawValue || '';
                        const code = extractCode(val);
                        if (code) {
                            scanLocked = true;
                            await stopScan();
                            await loadReturnByCode(code);
                        }
                    }
                } catch (e) {}
            }

            async function startNativeScan() {
                detector = new BarcodeDetector({ formats: ['qr_code', 'code_128', 'ean_13'] });
                if (!navigator.mediaDevices?.getUserMedia) {
                    throw new Error('no getUserMedia');
                }
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                html5Region.classList.add('hidden');
                video.classList.remove('hidden');
                video.srcObject = stream;
                scanLocked = false;
                timer = setInterval(tick, 600);
            }

            async function startHtml5Scan() {
                if (typeof Html5Qrcode === 'undefined') {
                    alert('Pustaka pemindai gagal dimuat. Muat ulang halaman lalu coba lagi.');
                    return;
                }
                if (!navigator.mediaDevices?.getUserMedia) {
                    alert('Kamera diblokir karena tidak menggunakan HTTPS.\n\nSOLUSI UNTUK IP LOKAL:\n1. Buka chrome://flags/#unsafely-treat-insecure-origin-as-secure\n2. Masukkan http://192.168.1.24:8000\n3. Set ke ENABLED & Relaunch Chrome.\n\nAtau gunakan "Scan dari foto" sebagai alternatif.');
                    return;
                }
                detector = null;
                video.classList.add('hidden');
                html5Region.classList.remove('hidden');
                scanLocked = false;
                html5Scanner = new Html5Qrcode('html5qr-reader');
                try {
                    await html5Scanner.start(
                        { facingMode: 'environment' },
                        { fps: 8, qrbox: { width: 220, height: 220 } },
                        async (decodedText) => {
                            if (scanLocked) return;
                            const code = extractCode(decodedText);
                            if (!code) return;
                            scanLocked = true;
                            await stopScan();
                            await loadReturnByCode(code);
                        },
                        () => {}
                    );
                } catch (e) {
                    video.classList.remove('hidden');
                    html5Region.classList.add('hidden');
                    if (html5Scanner) {
                        try { await html5Scanner.stop(); } catch (err) {}
                        try { html5Scanner.clear(); } catch (err) {}
                        html5Scanner = null;
                    }
                    console.error(e);
                    alert('Kamera tidak bisa dibuka. Pastikan izin kamera diberikan. Jika pakai IP lokal, aktifkan Flag "Insecure origins treated as secure" di Chrome terlebih dahulu.');
                }
            }

            async function startScan() {
                await stopScan();
                scanLocked = false;
                if ('BarcodeDetector' in window) {
                    try {
                        await startNativeScan();
                    } catch (e) {
                        console.warn(e);
                        await startHtml5Scan();
                    }
                } else {
                    await startHtml5Scan();
                }
            }

            async function stopScan() {
                if (timer) clearInterval(timer);
                timer = null;
                detector = null;
                if (html5Scanner) {
                    const h = html5Scanner;
                    html5Scanner = null;
                    try {
                        await h.stop();
                    } catch (e) {}
                    try {
                        h.clear();
                    } catch (e) {}
                }
                if (stream) {
                    stream.getTracks().forEach(t => t.stop());
                }
                stream = null;
                if (video) {
                    video.srcObject = null;
                    video.classList.remove('hidden');
                }
                html5Region.classList.add('hidden');
            }

            async function scanFromImageFile(file) {
                if (typeof Html5Qrcode === 'undefined') {
                    alert('Pustaka pemindai gagal dimuat. Muat ulang halaman.');
                    return;
                }
                const hostId = 'html5qr-file-host';
                const reader = new Html5Qrcode(hostId);
                try {
                    const text = await reader.scanFile(file, false);
                    const code = extractCode(text);
                    if (code) {
                        await loadReturnByCode(code);
                    } else {
                        alert('QR tidak terbaca. Pastikan gambar fokus dan berisi kode buku.');
                    }
                } catch (e) {
                    alert('QR tidak terbaca dari foto.');
                } finally {
                    try { await reader.clear(); } catch (e) {}
                }
            }

            startBtn.addEventListener('click', () => startScan().catch(console.error));
            stopBtn.addEventListener('click', () => stopScan());
            qrPhotoBtn?.addEventListener('click', () => {
                qrImageInput.value = '';
                qrImageInput.click();
            });
            qrImageInput?.addEventListener('change', () => {
                const file = qrImageInput.files?.[0];
                if (!file) return;
                scanFromImageFile(file).finally(() => { qrImageInput.value = ''; });
            });
            manualSearchBtn?.addEventListener('click', async () => {
                const code = extractCode(manualInput.value || '');
                if (!code) return alert('Kode tidak valid');
                await loadReturnByCode(code);
            });
            closeModalBtn?.addEventListener('click', () => {
                scanLocked = false;
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            });
            modal?.addEventListener('click', (e) => {
                if (e.target !== modal) return;
                scanLocked = false;
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            });
            setReturnButtonState(false);
            window.addEventListener('beforeunload', () => { stopScan(); });
        })();
    </script>
</x-app-layout>
