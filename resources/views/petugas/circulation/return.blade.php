<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Sirkulasi: Pengembalian Buku & Penetapan Sanksi
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-bold animate-fade-in flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-bold">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Scanner & Input Manual -->
                <div class="md:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <div>
                            <h3 class="font-black text-gray-900 text-lg">Scan Barcode / QR Pengembalian</h3>
                            <p class="text-xs text-gray-500 font-medium">Arahkan kamera ke QR Code buku untuk proses pengembalian dan cek keterlambatan.</p>
                        </div>
                        <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-black rounded-full uppercase">Kamera Aktif</span>
                    </div>

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

                    <!-- Input Manual Kode Buku -->
                    <div class="pt-4 border-t border-gray-100 space-y-2">
                        <label class="block text-xs font-black text-gray-700 uppercase tracking-wider">Atau Cari Manual Kode Buku</label>
                        <div class="flex gap-2">
                            <input type="text" id="manual-book-code" placeholder="Contoh: BK-0001 atau scan scanner USB..." class="w-full rounded-xl border-gray-200 text-sm uppercase font-mono">
                            <button id="manual-search-btn" type="button" class="px-5 py-2.5 bg-gray-900 hover:bg-black text-white text-xs font-black uppercase rounded-xl tracking-wider transition-all shadow-xs">Cari</button>
                        </div>
                    </div>
                </div>

                <!-- Antrian Terpanggil -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                    <h3 class="font-black text-gray-900 text-sm uppercase tracking-wider border-b border-gray-100 pb-3 flex items-center justify-between">
                        <span>Antrean Dipanggil (Called)</span>
                        <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full text-[10px]">{{ $calledQueues->count() }}</span>
                    </h3>
                    <div class="space-y-3">
                        @forelse($calledQueues as $queue)
                            <div class="border border-slate-100 rounded-xl p-3.5 bg-slate-50/50 space-y-2">
                                <p class="font-bold text-xs text-gray-900">{{ $queue->user->name }}</p>
                                <p class="text-[11px] text-gray-500 truncate">{{ $queue->book->title }}</p>
                                <div class="flex gap-2 pt-1">
                                    <form method="POST" action="{{ route('petugas.queues.notify', $queue) }}">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1.5 text-[10px] font-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white shadow-2xs">Kirim Notif</button>
                                    </form>
                                    <form method="POST" action="{{ route('petugas.queues.complete', $queue) }}">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1.5 text-[10px] font-bold rounded-lg bg-slate-800 hover:bg-black text-white shadow-2xs">Tandai Hadir</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 font-bold py-6 text-center">Belum ada antrean yang dipanggil.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL PROSES PENGEMBALIAN & PENETAPAN SANKSI -->
    <div id="return-modal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs hidden items-start justify-center z-50 p-4 sm:p-6 overflow-y-auto">
        <div class="bg-white w-full max-w-2xl rounded-[2.5rem] shadow-2xl my-6 overflow-hidden border border-gray-100">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-slate-50/50">
                <div>
                    <h3 class="text-lg font-black text-gray-900">Konfirmasi Pengembalian Buku</h3>
                    <p class="text-xs text-gray-500 font-medium">Verifikasi kondisi buku, status keterlambatan, dan sanksi siswa.</p>
                </div>
                <button id="close-return-modal" type="button" class="text-gray-400 hover:text-gray-600 font-black text-lg">✕</button>
            </div>

            <div class="p-6 sm:p-8 space-y-6 max-h-[80vh] overflow-y-auto">
                <!-- Data Buku -->
                <div id="return-book-card" class="bg-slate-50/80 rounded-2xl p-4 border border-slate-100 hidden">
                    <div class="flex gap-4">
                        <img id="return-book-cover" src="" alt="cover" class="h-28 w-20 object-cover rounded-xl border border-gray-200 hidden bg-white shadow-2xs">
                        <div class="flex-1 min-w-0 space-y-1">
                            <p class="font-black text-base text-gray-900 truncate" id="return-book-title">-</p>
                            <p class="text-xs text-gray-500 font-medium">Kode: <span id="return-book-code-text" class="font-mono font-bold text-gray-800">-</span></p>
                            <p class="text-xs text-gray-500 font-medium">Kategori: <span id="return-book-category" class="font-bold text-gray-700">-</span> | Rak: <span id="return-book-rack" class="font-bold text-gray-700">-</span></p>
                            <p class="text-xs text-gray-500 font-medium">Penulis: <span id="return-book-author" class="font-bold text-gray-700">-</span></p>
                        </div>
                    </div>
                </div>

                <!-- Data Peminjaman Aktif -->
                <div id="return-borrow-card" class="rounded-2xl p-5 border hidden space-y-3">
                    <h4 class="font-black text-xs uppercase tracking-wider text-gray-700">Rincian Transaksi Peminjam</h4>
                    <div id="return-borrow-content" class="text-xs text-gray-700 space-y-1.5"></div>
                </div>

                <!-- Form Pengembalian & Hukuman -->
                <div id="return-action-bar" class="hidden space-y-4">
                    <form method="POST" action="{{ route('petugas.circulation.return.store') }}" class="space-y-5">
                        @csrf
                        <input type="hidden" name="book_code" id="return-submit-book-code">

                        <!-- Kondisi Buku: Normal vs Hilang -->
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/70 space-y-3">
                            <label class="block text-xs font-black text-gray-800 uppercase tracking-wider">Kondisi Buku yang Dikembalikan</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="p-3 bg-white border border-gray-200 rounded-xl cursor-pointer flex items-center gap-2 text-xs font-bold text-gray-800 shadow-2xs">
                                    <input type="radio" name="return_condition" value="normal" checked onchange="toggleConditionFields(this.value)">
                                    <span>📗 Buku Kembali (Normal)</span>
                                </label>
                                <label class="p-3 bg-white border border-gray-200 rounded-xl cursor-pointer flex items-center gap-2 text-xs font-bold text-red-700 shadow-2xs">
                                    <input type="radio" name="return_condition" value="lost" onchange="toggleConditionFields(this.value)">
                                    <span>📕 Buku Hilang (Lost)</span>
                                </label>
                            </div>

                            <!-- Input Denda Kehilangan Kustom jika Buku Hilang -->
                            <div id="lost-fine-section" class="hidden pt-3 border-t border-slate-200 space-y-2">
                                <label class="block text-xs font-black text-red-700 uppercase tracking-wider">Nominal Denda Ganti Rugi Buku Hilang (Rp)</label>
                                <input type="number" name="lost_fine_amount" value="50000" min="0" step="1000" class="w-full text-xs font-bold rounded-xl border-red-300 focus:border-red-500 focus:ring-red-500" placeholder="Masukkan denda kehilangan (Contoh: 50000)">
                                <p class="text-[10px] text-gray-500">Pustakawan / Admin bebas menentukan besaran denda ganti rugi sesuai harga buku.</p>
                            </div>
                        </div>

                        <!-- Opsi Hukuman jika Terlambat -->
                        <div id="late-punishment-section" class="hidden p-4 bg-amber-50/60 rounded-2xl border border-amber-200 space-y-3">
                            <label class="block text-xs font-black text-amber-900 uppercase tracking-wider">Pilih Sanksi Keterlambatan untuk Siswa</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="p-3 bg-white border border-amber-300 rounded-xl cursor-pointer flex items-center gap-2 text-xs font-bold text-gray-800 shadow-2xs">
                                    <input type="radio" name="punishment_type" value="fine" checked onchange="togglePunishmentFields(this.value)">
                                    <span>💰 Denda (Rp 15.000/Hari)</span>
                                </label>
                                <label class="p-3 bg-white border border-amber-300 rounded-xl cursor-pointer flex items-center gap-2 text-xs font-bold text-amber-800 shadow-2xs">
                                    <input type="radio" name="punishment_type" value="social" onchange="togglePunishmentFields(this.value)">
                                    <span>🧹 Hukuman Sosial</span>
                                </label>
                            </div>

                            <div id="social-punishment-input-section" class="hidden pt-2 space-y-1.5">
                                <label class="block text-[11px] font-black text-amber-900 uppercase tracking-wider">Deskripsi Tugas Sosial Siswa</label>
                                <input type="text" name="social_punishment_description" placeholder="Contoh: Membersihkan toilet lantai 2 / Menata rak novel" class="w-full text-xs rounded-xl border-amber-300 focus:ring-amber-500 focus:border-amber-500" value="Membersihkan toilet lantai 2">
                            </div>
                        </div>

                        <div class="pt-2">
                            <button
                                type="submit"
                                id="confirm-return-btn"
                                class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-black uppercase tracking-wider shadow-lg shadow-emerald-200 transition-all active:scale-95 flex items-center justify-center gap-2"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                <span>Konfirmasi Pengembalian Buku</span>
                            </button>
                        </div>
                    </form>
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
            const returnBookCategory = document.getElementById('return-book-category');
            const returnBookRack = document.getElementById('return-book-rack');
            const returnBookAuthor = document.getElementById('return-book-author');
            const lostFineSection = document.getElementById('lost-fine-section');
            const latePunishmentSection = document.getElementById('late-punishment-section');
            const socialPunishmentInputSection = document.getElementById('social-punishment-input-section');

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

            window.toggleConditionFields = function(val) {
                if (val === 'lost') {
                    lostFineSection?.classList.remove('hidden');
                    latePunishmentSection?.classList.add('hidden');
                } else {
                    lostFineSection?.classList.add('hidden');
                }
            };

            window.togglePunishmentFields = function(val) {
                if (val === 'social') {
                    socialPunishmentInputSection?.classList.remove('hidden');
                } else {
                    socialPunishmentInputSection?.classList.add('hidden');
                }
            };

            async function loadReturnByCode(code) {
                try {
                    const response = await fetch(`${returnScanBase}/${encodeURIComponent(code)}`);
                    if (!response.ok) return alert('Buku tidak ditemukan');
                    const payload = await response.json();
                    const book = payload.book;
                    const borrow = payload.borrow;

                    returnBookTitle.textContent = book.title || '-';
                    returnBookCodeText.textContent = book.code || '-';
                    returnBookCategory.textContent = book.category || '-';
                    returnBookRack.textContent = book.rack_code || '-';
                    returnBookAuthor.textContent = book.author || '-';
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
                        returnBorrowCard.className = 'rounded-2xl p-5 border border-slate-200 bg-slate-50 text-slate-600';
                        returnBorrowContent.innerHTML = '<div class="text-xs font-bold text-gray-500">Tidak ada transaksi pinjam aktif untuk buku ini.</div>';
                        returnActionBar.classList.add('hidden');
                        return;
                    }

                    const isLate = borrow.late_days > 0;
                    returnBorrowCard.classList.remove('hidden');
                    returnBorrowCard.className = isLate 
                        ? 'rounded-2xl p-5 border border-red-200 bg-red-50/50 text-red-900' 
                        : 'rounded-2xl p-5 border border-emerald-200 bg-emerald-50/50 text-emerald-900';

                    let lateInfoHtml = '';
                    if (isLate) {
                        latePunishmentSection?.classList.remove('hidden');
                        lateInfoHtml = `
                            <div class="p-3 bg-red-100/70 rounded-xl border border-red-200 mt-2 space-y-1">
                                <p class="font-black text-red-800 uppercase text-[11px]">⚠️ TERLAMBAT ${borrow.late_days} HARI</p>
                                <p class="text-xs font-bold text-red-700">Denda Keterlambatan: Rp ${Number(borrow.fine_preview || 0).toLocaleString('id-ID')} (@ Rp 15.000/hari)</p>
                                ${borrow.late_reason ? `<p class="text-xs text-gray-700 mt-1"><strong>Alasan Murid:</strong> "${borrow.late_reason}"</p>` : ''}
                                ${borrow.late_evidence ? `<p class="mt-1"><a href="/petugas/borrows/${borrow.id}/file/evidence" target="_blank" class="text-xs text-indigo-600 font-bold underline">Lihat Foto Surat Dokter / Bukti ↗</a></p>` : ''}
                            </div>
                        `;
                    } else {
                        latePunishmentSection?.classList.add('hidden');
                    }

                    returnBorrowContent.innerHTML = `
                        <div><strong>Peminjam:</strong> ${borrow.borrower.name || '-'} (Kelas: ${borrow.borrower.kelas || '-'})</div>
                        <div><strong>Tanggal Pinjam:</strong> ${borrow.borrow_date || '-'}</div>
                        <div><strong>Jatuh Tempo:</strong> ${borrow.due_date || '-'}</div>
                        <div><strong>Status Waktu:</strong> <span class="font-black ${isLate ? 'text-red-600' : 'text-emerald-600'}">${borrow.time_status}</span></div>
                        ${lateInfoHtml}
                    `;

                    returnActionBar.classList.remove('hidden');
                } catch (err) {
                    console.error(err);
                    alert('Gagal memuat data pengembalian');
                }
            }

            async function tick() {
                if (!detector || !video || video.readyState < 2 || scanLocked) return;
                try {
                    const barcodes = await detector.detect(video);
                    if (!barcodes.length) return;
                    const code = extractCode(barcodes[0].rawValue || '');
                    if (!code) return;
                    scanLocked = true;
                    await stopScan();
                    await loadReturnByCode(code);
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
                timer = setInterval(tick, 200);
                startBtn.classList.add('hidden');
                stopBtn.classList.remove('hidden');
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
                    startBtn.classList.add('hidden');
                    stopBtn.classList.remove('hidden');
                } catch (e) {
                    video.classList.remove('hidden');
                    html5Region.classList.add('hidden');
                    if (html5Scanner) {
                        try { await html5Scanner.stop(); } catch (err) {}
                        try { html5Scanner.clear(); } catch (err) {}
                        html5Scanner = null;
                    }
                    console.error(e);
                    alert('Kamera tidak bisa dibuka. Pastikan izin kamera diberikan. Jika pakai IP lokal, aktifkan Flag Chrome terlebih dahulu.');
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
                startBtn.classList.remove('hidden');
                stopBtn.classList.add('hidden');
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

            startBtn?.addEventListener('click', () => startScan().catch(console.error));
            stopBtn?.addEventListener('click', () => stopScan());
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
                returnActionBar.classList.add('hidden');
                document.body.style.overflow = '';
            });
            modal?.addEventListener('click', (e) => {
                if (e.target !== modal) return;
                scanLocked = false;
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            });
            window.addEventListener('beforeunload', () => { stopScan(); });

            window.addEventListener('DOMContentLoaded', () => {
                const urlParams = new URLSearchParams(window.location.search);
                const code = urlParams.get('code');
                if (code) {
                    loadReturnByCode(code);
                }
            });
        })();
    </script>
</x-app-layout>
