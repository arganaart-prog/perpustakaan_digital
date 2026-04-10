<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mode Peminjaman</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 space-y-6">
            @if (session('success'))
                <div class="px-4 py-3 bg-green-100 border border-green-300 text-green-800 rounded">{{ session('success') }}</div>
            @endif

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
                            Akses lewat <code class="text-[11px]">http://IP-lokal</code> memerlukan <strong>HTTPS</strong> atau konfigurasi manual (Flags).<br>
                            <strong>Aktifkan Kamera:</strong> Buka <code class="text-[11px] select-all">chrome://flags/#unsafely-treat-insecure-origin-as-secure</code>, masukkan <code class="text-[11px] select-all">http://192.168.1.24:8000</code>, pilih <strong>Enabled</strong>, lalu restart Chrome.
                        </p>
                    </div>
                    <div class="border rounded p-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Input kode manual</label>
                        <div class="flex gap-2">
                            <input type="text" id="manual-book-code" placeholder="BK001" class="w-full border-gray-300 rounded-md">
                            <button type="button" id="manual-search-btn" class="px-4 py-2 bg-indigo-600 text-white rounded">Cari</button>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Scan akan langsung memunculkan popup detail buku.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="loan-modal" class="fixed inset-0 bg-black/50 hidden items-start justify-center z-50 p-4 overflow-y-auto">
        <div class="bg-white w-full max-w-4xl rounded-xl shadow-xl my-4">
            <div class="p-5 border-b flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Detail Buku & Peminjaman</h3>
                <button id="close-loan-modal" type="button" class="text-gray-500 hover:text-gray-700">✕</button>
            </div>
            <div class="p-5 space-y-5">
                <div id="book-card" class="border rounded p-4 hidden">
                    <div class="flex gap-4">
                        <img id="book-cover" src="" alt="cover" class="h-28 w-20 object-cover rounded border border-gray-200 hidden" style="width:80px;height:112px;object-fit:cover;">
                        <div>
                            <p class="font-bold text-lg" id="book-title">-</p>
                            <p class="text-sm text-gray-600">Kode: <span id="book-code-text">-</span></p>
                            <p class="text-sm text-gray-600">Status:
                                <span id="book-status" class="font-semibold inline-block px-2 py-0.5 rounded text-xs">-</span>
                            </p>
                            <p class="text-sm text-gray-600">Kategori: <span id="book-category">-</span> | Rak: <span id="book-rack">-</span></p>
                            <p class="text-sm text-gray-600">Penulis: <span id="book-author">-</span> | Hal: <span id="book-pages">-</span></p>
                        </div>
                    </div>
                </div>

                <div id="loan-action-bar" class="hidden border-2 border-blue-300 bg-blue-50 rounded p-4">
                    <button
                        type="button"
                        id="open-borrower-step-btn"
                        class="inline-flex items-center justify-center px-5 py-2.5 text-white rounded text-sm font-semibold border cursor-not-allowed shadow-sm"
                        style="display:inline-flex;min-width:220px;background:#9ca3af;border-color:#6b7280;"
                        disabled
                    >
                        Tidak bisa dipinjam
                    </button>
                </div>

                <div id="borrower-step" class="border rounded p-4 hidden">
                    <p class="font-semibold mb-2">Cari peminjam (Nama / ID akun)</p>
                    <div class="flex gap-2">
                        <input type="text" id="borrower-search-input" class="w-full border-gray-300 rounded-md" placeholder="Contoh: Budi atau 12">
                        <button type="button" id="borrower-search-btn" class="px-3 py-2 bg-gray-800 text-white rounded text-sm">Cari</button>
                    </div>
                    <div id="borrower-results" class="mt-3 space-y-2"></div>
                </div>

                <div id="borrower-summary-card" class="border rounded p-4 hidden">
                    <p class="font-semibold text-gray-900 mb-2">Ringkasan Akun Peminjam</p>
                    <div id="borrower-summary-content" class="text-sm text-gray-700 space-y-1"></div>
                    <div class="mt-3">
                        <form method="POST" action="{{ route('petugas.circulation.loan.store') }}">
                            @csrf
                            <input type="hidden" name="book_code" id="submit-book-code">
                            <input type="hidden" name="user_id" id="submit-user-id">
                            <div class="mb-3">
                                <label for="submit-duration-days" class="block text-sm font-medium text-gray-700 mb-1">Durasi peminjaman</label>
                                <select name="duration_days" id="submit-duration-days" class="w-full border-gray-300 rounded-md" required>
                                    <option value="4">4 hari</option>
                                    <option value="6">6 hari</option>
                                    <option value="7" selected>7 hari</option>
                                    <option value="12">12 hari</option>
                                    <option value="15">15 hari</option>
                                    <option value="16">16 hari</option>
                                </select>
                            </div>
                            <button type="submit" id="confirm-loan-btn" class="px-4 py-2 bg-indigo-600 text-white rounded text-sm">Konfirmasi Pinjam</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js" crossorigin="anonymous"></script>
    <script>
        (function () {
            const scanUrlBase = @json(url('/petugas/circulation/scan'));
            const borrowerSearchUrl = @json(route('petugas.circulation.borrower.search'));
            const borrowerSummaryBase = @json(url('/petugas/circulation/borrower'));

            const video = document.getElementById('camera-preview');
            const html5Region = document.getElementById('html5qr-reader');
            const startBtn = document.getElementById('start-scan-btn');
            const stopBtn = document.getElementById('stop-scan-btn');
            const qrPhotoBtn = document.getElementById('qr-photo-btn');
            const qrImageInput = document.getElementById('qr-image-input');
            const manualInput = document.getElementById('manual-book-code');
            const manualSearchBtn = document.getElementById('manual-search-btn');

            const modal = document.getElementById('loan-modal');
            const closeModalBtn = document.getElementById('close-loan-modal');
            const bookCard = document.getElementById('book-card');
            const loanActionBar = document.getElementById('loan-action-bar');
            const openBorrowerStepBtn = document.getElementById('open-borrower-step-btn');
            const borrowerStep = document.getElementById('borrower-step');
            const borrowerSearchInput = document.getElementById('borrower-search-input');
            const borrowerSearchBtn = document.getElementById('borrower-search-btn');
            const borrowerResults = document.getElementById('borrower-results');
            const borrowerSummaryCard = document.getElementById('borrower-summary-card');
            const borrowerSummaryContent = document.getElementById('borrower-summary-content');
            const submitBookCode = document.getElementById('submit-book-code');
            const submitUserId = document.getElementById('submit-user-id');
            const confirmLoanBtn = document.getElementById('confirm-loan-btn');

            const bookCover = document.getElementById('book-cover');
            const bookTitle = document.getElementById('book-title');
            const bookCodeText = document.getElementById('book-code-text');
            const bookStatus = document.getElementById('book-status');
            const bookCategory = document.getElementById('book-category');
            const bookRack = document.getElementById('book-rack');
            const bookAuthor = document.getElementById('book-author');
            const bookPages = document.getElementById('book-pages');

            let stream = null;
            let detector = null;
            let timer = null;
            let html5Scanner = null;
            let scanLocked = false;
            let audioCtx = null;

            function setBorrowerStepButtonState(canStartLoan) {
                openBorrowerStepBtn.style.display = 'inline-flex';
                openBorrowerStepBtn.style.visibility = 'visible';
                openBorrowerStepBtn.style.opacity = '1';
                openBorrowerStepBtn.disabled = !canStartLoan;
                openBorrowerStepBtn.textContent = canStartLoan ? 'Lanjut Peminjaman' : 'Tidak bisa dipinjam';

                if (canStartLoan) {
                    openBorrowerStepBtn.style.background = '#16a34a';
                    openBorrowerStepBtn.style.borderColor = '#15803d';
                    openBorrowerStepBtn.style.cursor = 'pointer';
                } else {
                    openBorrowerStepBtn.style.background = '#9ca3af';
                    openBorrowerStepBtn.style.borderColor = '#6b7280';
                    openBorrowerStepBtn.style.cursor = 'not-allowed';
                }
            }

            function extractCode(raw) {
                if (!raw) return '';
                const match = raw.match(/\/circulation\/book\/([^/?#]+)/i);
                if (match && match[1]) return decodeURIComponent(match[1]).toUpperCase();
                const simpleCode = raw.trim().toUpperCase();
                if (/^[A-Z0-9-]{3,30}$/.test(simpleCode)) return simpleCode;
                return '';
            }

            async function loadBookByCode(code) {
                const response = await fetch(`${scanUrlBase}/${encodeURIComponent(code)}`);
                if (!response.ok) return alert('Buku tidak ditemukan');
                const payload = await response.json();
                const book = payload.book;

                bookTitle.textContent = book.title || '-';
                bookCodeText.textContent = book.code || '-';
                bookStatus.textContent = book.status || '-';
                setStatusBadge(book.status || '-');
                bookCategory.textContent = book.category || '-';
                bookRack.textContent = book.rack_code || '-';
                bookAuthor.textContent = book.author || '-';
                bookPages.textContent = book.pages || '-';
                submitBookCode.value = book.code || '';

                if (book.cover_url) {
                    bookCover.src = book.cover_url;
                    bookCover.classList.remove('hidden');
                } else {
                    bookCover.classList.add('hidden');
                    bookCover.src = '';
                }

                bookCard.classList.remove('hidden');
                loanActionBar.classList.remove('hidden');
                borrowerStep.classList.add('hidden');
                borrowerSummaryCard.classList.add('hidden');
                borrowerResults.innerHTML = '';
                const canStartLoan = (book.status || '').toUpperCase() === 'AVAILABLE';
                setBorrowerStepButtonState(canStartLoan);
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
                playBeep();
            }

            async function searchBorrower() {
                const q = borrowerSearchInput.value.trim();
                if (!q) return;
                const response = await fetch(`${borrowerSearchUrl}?q=${encodeURIComponent(q)}`);
                const payload = await response.json();
                const items = payload.items || [];
                if (!items.length) {
                    borrowerResults.innerHTML = '<div class="text-sm text-gray-500">Akun tidak ditemukan.</div>';
                    return;
                }

                borrowerResults.innerHTML = items.map(item => `
                    <button type="button" class="w-full text-left border rounded p-3 hover:bg-gray-50 borrower-item" data-id="${item.id}">
                        <div class="font-semibold">${item.name}</div>
                        <div class="text-xs text-gray-600">ID: ${item.id} | ${item.kelas || '-'} / ${item.jurusan || '-'} | tipe: ${item.member_type || '-'}</div>
                    </button>
                `).join('');
            }

            async function loadBorrowerSummary(userId) {
                const response = await fetch(`${borrowerSummaryBase}/${userId}`);
                const payload = await response.json();
                const u = payload.user;
                const b = payload.borrow;
                const r = payload.risk;
                const history = payload.recent_history || [];

                const historyHtml = history.length
                    ? `<ul class="list-disc pl-5">${history.map(h => `<li>${h.book || '-'} (${h.status}) | denda: Rp ${Number(h.fine).toLocaleString('id-ID')}</li>`).join('')}</ul>`
                    : '<div>Tidak ada riwayat.</div>';

                borrowerSummaryContent.innerHTML = `
                    <div><strong>Nama:</strong> ${u.name} (ID: ${u.id})</div>
                    <div><strong>Kelas/Jurusan:</strong> ${u.kelas || '-'} / ${u.jurusan || '-'}</div>
                    <div><strong>Tipe:</strong> ${u.member_type || '-'}</div>
                    <div><strong>Status pinjam:</strong> ${b.active_count} aktif dari maksimal ${b.max} (sisa ${b.remaining})</div>
                    <div><strong>Denda belum lunas:</strong> Rp ${Number(r.unpaid_fine).toLocaleString('id-ID')}</div>
                    <div><strong>Riwayat telat:</strong> ${r.late_history_count} kali | <strong>Riwayat hilang:</strong> ${r.lost_history_count} kali</div>
                    <div><strong>Riwayat terbaru:</strong> ${historyHtml}</div>
                `;

                submitUserId.value = u.id;
                borrowerSummaryCard.classList.remove('hidden');

                const blocked = Number(r.unpaid_fine) > 0 || Number(b.remaining) <= 0;
                confirmLoanBtn.disabled = blocked;
                confirmLoanBtn.classList.toggle('bg-gray-400', blocked);
                confirmLoanBtn.classList.toggle('cursor-not-allowed', blocked);
                confirmLoanBtn.classList.toggle('bg-indigo-600', !blocked);
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
                    await loadBookByCode(code);
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
                timer = setInterval(() => tick().catch(() => {}), 600);
            }

            async function startHtml5Scan() {
                if (typeof Html5Qrcode === 'undefined') {
                    alert('Pustaka pemindai gagal dimuat. Muat ulang halaman lalu coba lagi.');
                    return;
                }
                if (!navigator.mediaDevices?.getUserMedia) {
                    alert('Kamera diblokir (HTTPS diperlukan).\n\nSOLUSI UNTUK IP LOKAL:\n1. Buka chrome://flags/#unsafely-treat-insecure-origin-as-secure\n2. Masukkan http://192.168.1.24:8000\n3. Set ke ENABLED lalu restart Chrome.\n\nAtau gunakan tombol "Scan dari foto".');
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
                            await loadBookByCode(code);
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
                    alert('Gagal membuka kamera. Pastikan izin kamera diberikan dan IP lokal sudah didaftarkan di Chrome Flags jika tidak menggunakan HTTPS.');
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
                const reader = new Html5Qrcode('html5qr-file-host');
                try {
                    const text = await reader.scanFile(file, false);
                    const code = extractCode(text);
                    if (code) {
                        await loadBookByCode(code);
                    } else {
                        alert('QR tidak terbaca. Pastikan gambar fokus dan berisi kode buku.');
                    }
                } catch (e) {
                    alert('QR tidak terbaca dari foto.');
                } finally {
                    try { await reader.clear(); } catch (e) {}
                }
            }

            function setStatusBadge(status) {
                const s = String(status || '').toUpperCase();
                bookStatus.classList.remove(
                    'bg-green-100', 'text-green-700',
                    'bg-red-100', 'text-red-700',
                    'bg-yellow-100', 'text-yellow-700',
                    'bg-gray-100', 'text-gray-700'
                );

                if (s === 'AVAILABLE') {
                    bookStatus.classList.add('bg-green-100', 'text-green-700');
                } else if (s === 'BORROWED' || s === 'LOST') {
                    bookStatus.classList.add('bg-red-100', 'text-red-700');
                } else if (s === 'RESERVED') {
                    bookStatus.classList.add('bg-yellow-100', 'text-yellow-700');
                } else {
                    bookStatus.classList.add('bg-gray-100', 'text-gray-700');
                }
            }

            function playBeep() {
                try {
                    if (!audioCtx) {
                        audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    }
                    const osc = audioCtx.createOscillator();
                    const gain = audioCtx.createGain();
                    osc.type = 'sine';
                    osc.frequency.value = 920;
                    gain.gain.value = 0.06;
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    osc.start();
                    osc.stop(audioCtx.currentTime + 0.12);
                } catch (e) {}
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
                await loadBookByCode(code);
            });
            closeModalBtn?.addEventListener('click', () => {
                scanLocked = false;
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                loanActionBar.classList.add('hidden');
                document.body.style.overflow = '';
            });
            openBorrowerStepBtn?.addEventListener('click', () => {
                if (openBorrowerStepBtn.disabled) return;
                borrowerStep.classList.remove('hidden');
                borrowerSummaryCard.classList.add('hidden');
                borrowerSearchInput?.focus();
            });
            borrowerSearchBtn?.addEventListener('click', searchBorrower);
            borrowerResults?.addEventListener('click', async (e) => {
                const target = e.target.closest('.borrower-item');
                if (!target) return;
                await loadBorrowerSummary(target.dataset.id);
            });
            setBorrowerStepButtonState(false);
            window.addEventListener('beforeunload', () => { stopScan(); });
            modal?.addEventListener('click', (e) => {
                if (e.target !== modal) return;
                scanLocked = false;
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                loanActionBar.classList.add('hidden');
                document.body.style.overflow = '';
            });
        })();
    </script>
</x-app-layout>
