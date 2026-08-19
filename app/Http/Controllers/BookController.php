<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookQueue;
use App\Models\Borrow;
use App\Models\LabelColor;
use App\Models\Rack;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\BookReview;

class BookController extends Controller
{
    /**
     * index: Menampilkan halaman utama manajemen buku untuk Admin & Petugas.
     * Mengambil data buku dengan paginasi, serta data master (kategori, rak, warna label).
     */
    public function index(Request $request)
    {
        $routePrefix = auth()->user()->hasRole('admin') ? 'admin' : 'petugas';
        
        $query = Book::query();

        // Search
        if ($request->has('q')) {
            $q = $request->get('q');
            $query->where(function($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('author', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%")
                    ->orWhere('isbn', 'like', "%{$q}%");
            });
        }

        // Filter Status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $query->withCount(['queues as active_queues_count' => function ($q) {
            $q->whereIn('status', [\App\Models\BookQueue::STATUS_WAITING, \App\Models\BookQueue::STATUS_READY, \App\Models\BookQueue::STATUS_CALLED]);
        }]);

        $books = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $routePrefix = $request->routeIs('admin.*') ? 'admin' : 'petugas';
        $panelTitle = $routePrefix === 'admin' ? 'Manajemen Buku (Admin)' : 'Manajemen Buku (Petugas)';
        $labelColors = LabelColor::orderBy('name')->get();
        $racks = Rack::orderBy('code')->get();
        $categories = BookCategory::orderBy('name')->get();

        return view('books.manage', compact('books', 'routePrefix', 'panelTitle', 'labelColors', 'racks', 'categories'));
    }

    /**
     * store: Menyimpan data buku baru ke database.
     * Termasuk generate kode buku otomatis dan upload cover jika ada.
     */
    public function store(Request $request)
    {
        // Validasi input form
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:50', 'unique:books,isbn'],
            'pages' => ['required', 'integer', 'min:1'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
            'category' => ['nullable', 'string', 'max:100', Rule::exists('book_categories', 'name')],
            'rack_code' => ['nullable', 'string', 'max:30', Rule::exists('racks', 'code')],
            'label_color' => ['nullable', 'string', 'max:30', Rule::exists('label_colors', 'name')],
            'exemplar_no' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', Rule::in([
                Book::STATUS_AVAILABLE,
                Book::STATUS_BORROWED,
                Book::STATUS_RESERVED,
                Book::STATUS_LOST,
            ])],
        ], [
            'title.required' => 'Judul buku wajib diisi.',
            'isbn.unique' => 'ISBN sudah terdaftar.',
            'pages.required' => 'Jumlah halaman wajib diisi.',
            'cover_image.image' => 'File cover harus berupa gambar.',
            'status.required' => 'Status buku wajib dipilih.',
        ]);

        // Automasi data tambahan
        $validated['code'] = $this->generateBookCode(); // Buat kode BK001 dst.
        $validated['exemplar_no'] = (int) ($validated['exemplar_no'] ?? 1);
        $validated['label_color'] = $this->resolveLabelColorByCategory($validated['category'] ?? null); // Warna otomatis dari kategori

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('book-covers', 'public');
        }

        Book::create($validated);

        return back()->with('success', 'Buku berhasil ditambahkan.');
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:50', Rule::unique('books', 'isbn')->ignore($book->id)],
            'pages' => ['required', 'integer', 'min:1'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
            'category' => ['nullable', 'string', 'max:100', Rule::exists('book_categories', 'name')],
            'rack_code' => ['nullable', 'string', 'max:30', Rule::exists('racks', 'code')],
            'label_color' => ['nullable', 'string', 'max:30', Rule::exists('label_colors', 'name')],
            'exemplar_no' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', Rule::in([
                Book::STATUS_AVAILABLE,
                Book::STATUS_BORROWED,
                Book::STATUS_RESERVED,
                Book::STATUS_LOST,
            ])],
        ], [
            'title.required' => 'Judul buku wajib diisi.',
            'isbn.unique' => 'ISBN sudah terdaftar.',
            'pages.required' => 'Jumlah halaman wajib diisi.',
            'cover_image.image' => 'File cover harus berupa gambar.',
            'status.required' => 'Status buku wajib dipilih.',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('book-covers', 'public');
        }

        $validated['code'] = $book->code;
        if (isset($validated['exemplar_no'])) {
            $validated['exemplar_no'] = (int) $validated['exemplar_no'];
        }
        $validated['label_color'] = $this->resolveLabelColorByCategory($validated['category'] ?? null);

        $book->update($validated);

        return back()->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy(Book $book)
    {
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }
        $book->delete();

        return back()->with('success', 'Buku berhasil dihapus.');
    }

    public function memberDashboard()
    {
        $user = auth()->user();
        
        // Statistik Member
        $activeLoansCount = Borrow::where('user_id', $user->id)
            ->whereIn('status', [Borrow::STATUS_ACTIVE, Borrow::STATUS_LATE])
            ->count();
            
        $readyQueuesCount = BookQueue::where('user_id', $user->id)
            ->where('status', BookQueue::STATUS_READY)
            ->count();

        // Data Denda Aktif (Unpaid)
        $unpaidFinesCount = Borrow::where('user_id', $user->id)
            ->where('fine', '>', 0)
            ->whereNull('fine_paid_at')
            ->count();

        $totalUnpaidFineAmount = Borrow::where('user_id', $user->id)
            ->where('fine', '>', 0)
            ->whereNull('fine_paid_at')
            ->sum('fine');

        $activeFines = Borrow::with('book')
            ->where('user_id', $user->id)
            ->where('fine', '>', 0)
            ->whereNull('fine_paid_at')
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function($f) {
                // Tentukan alasan denda
                $reason = "Keterlambatan Pengembalian";
                if ($f->status === Borrow::STATUS_LOST) $reason = "Buku Hilang / Rusak";
                
                // Hitung keterlambatan hari
                $lateDays = 0;
                if ($f->return_date && $f->due_date) {
                    $lateDays = max(0, $f->due_date->startOfDay()->diffInDays($f->return_date->startOfDay(), false));
                }

                return [
                    'id' => $f->id,
                    'book_title' => $f->book?->title ?? 'Buku dihapus',
                    'due_date' => $f->due_date?->format('d M Y'),
                    'return_at' => $f->return_date?->format('d M Y') ?? '-',
                    'amount' => (int) $f->fine,
                    'reason' => $lateDays > 0 ? "Terlambat {$lateDays} hari" : $reason,
                ];
            });

        // Buku Trending (Paling banyak dipinjam secara global)
        $trendingBooks = Book::withCount('borrows')
            ->orderBy('borrows_count', 'desc')
            ->take(6)
            ->get();

        // Buku Baru
        $newArrivals = Book::latest()->take(6)->get();

        // Ambil Notifikasi Peminjaman Baru (untuk pop-up)
        $newLoanNotification = $user->unreadNotifications()
            ->where('data->type', 'borrow_started')
            ->latest()
            ->first();

        return view('dashboard', compact(
            'activeLoansCount', 
            'readyQueuesCount', 
            'unpaidFinesCount',
            'totalUnpaidFineAmount',
            'activeFines',
            'trendingBooks', 
            'newArrivals',
            'newLoanNotification'
        ));
    }

    /**
     * memberIndex: Menampilkan daftar buku untuk Member (Siswa/Guru).
     * Menggunakan data yang sudah dimap (dipetakan) agar ringan dibuka di HP.
     */
    public function memberIndex()
    {
        $myQueues = BookQueue::query()
            ->where('user_id', auth()->id())
            ->whereIn('status', [
                BookQueue::STATUS_WAITING,
                BookQueue::STATUS_READY,
                BookQueue::STATUS_CALLED,
            ])
            ->get()
            ->keyBy('book_id');

        return view('books.member-index', compact('myQueues'));
    }

    /**
     * memberBookDetail: Menampilkan halaman detail buku untuk member
     */
    /**
     * memberBookDetail: Menampilkan halaman detail buku untuk member
     */
    public function memberBookDetail(Book $book)
    {
        $detailData = $this->buildBookDetailData($book);
        return view('books.book-detail', [
            'bookData' => $detailData['book'],
            'relatedBooks' => $detailData['related'],
        ]);
    }

    /**
     * apiIndex: Mengambil daftar buku via JSON untuk katalog mobile.
     */
    public function apiIndex(Request $request)
    {
        $query = Book::query();

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $books = $query->withCount(['borrows', 'approvedReviews'])
            ->withAvg('approvedReviews', 'rating')
            ->orderBy('title')
            ->get()
            ->map(function($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'code' => $book->code,
                    'isbn' => $book->isbn,
                    'pages' => $book->pages,
                    'status' => $book->status,
                    'category' => $book->category,
                    'rack_code' => $book->rack_code,
                    'exemplar_no' => $book->exemplar_no,
                    'label_color' => $book->label_color,
                    'cover_url' => $book->cover_image ? route('books.cover', $book) : null,
                    'borrows_count' => $book->borrows_count,
                    'avg_rating' => (float) ($book->approved_reviews_avg_rating ?? 0),
                    'reviews_count' => $book->approved_reviews_count,
                ];
            });

        return response()->json($books);
    }

    /**
     * apiDetail: Mengambil detail lengkap buku termasuk ulasan yang sudah diapprove dan buku terkait.
     */
    public function apiDetail(Book $book)
    {
        return response()->json($this->buildBookDetailData($book));
    }

    private function buildBookDetailData(Book $book): array
    {
        // Ambil ulasan milik user saat ini (meskipun masih pending)
        $userReview = BookReview::where('user_id', auth()->id())
            ->where('book_id', $book->id)
            ->where('is_approved', false)
            ->first();

        $book->load(['approvedReviews.user']);
        $book->loadCount(['borrows', 'approvedReviews']);

        // BUG FIX: Gunakan rerata dari SEMUA ulasan agar rating langsung terasa perubahannya oleh user
        $realAvgRating = \App\Models\BookReview::where('book_id', $book->id)->avg('rating');

        // STOK INFO: Hitung buku yang tersedia dengan JUDUL YANG SAMA
        $totalStock = Book::where('title', $book->title)->count();
        $availableStock = Book::where('title', $book->title)
            ->where('status', Book::STATUS_AVAILABLE)
            ->count();

        // Buku Terkait (berdasarkan kategori yang sama, exclude buku ini)
        $relatedBooks = Book::where('category', $book->category)
            ->where('id', '!=', $book->id)
            ->withCount('borrows')
            ->withAvg('approvedReviews', 'rating')
            ->take(5)
            ->get()
            ->map(function($b) {
                return [
                    'id' => $b->id,
                    'title' => $b->title,
                    'author' => $b->author,
                    'cover_url' => $b->cover_image ? route('books.cover', $b) : null,
                    'avg_rating' => (float) ($b->approved_reviews_avg_rating ?? 0),
                ];
            });

        // Cek apakah member ini berhak mengulas (sudah pernah mengembalikan buku ini)
        $canReview = \App\Models\Borrow::where('user_id', auth()->id())
            ->where('book_id', $book->id)
            ->where('status', \App\Models\Borrow::STATUS_RETURNED)
            ->exists();

        // Antrean Aktif
        $activeQueuesCount = \App\Models\BookQueue::where('book_id', $book->id)
            ->whereIn('status', [\App\Models\BookQueue::STATUS_WAITING, \App\Models\BookQueue::STATUS_READY, \App\Models\BookQueue::STATUS_CALLED])
            ->count();

        // Posisi antrean user ini
        $userQueuePos = null;
        $userQueueEstimatedDate = null;
        $userQueue = \App\Models\BookQueue::where('book_id', $book->id)
            ->where('user_id', auth()->id())
            ->whereIn('status', [\App\Models\BookQueue::STATUS_WAITING, \App\Models\BookQueue::STATUS_READY, \App\Models\BookQueue::STATUS_CALLED])
            ->first();
        
        if ($userQueue) {
             $userQueuePos = $userQueue->getQueuePosition();
             $userQueueEstimatedDate = $userQueue->getEstimatedAvailableDate()->format('d M Y');
        }

        // Estimasi jika user baru mau booking sekarang
        $activeBorrow = Borrow::where('book_id', $book->id)
            ->whereIn('status', [Borrow::STATUS_ACTIVE, Borrow::STATUS_LATE])
            ->latest('due_date')
            ->first();
        $baseDate = $activeBorrow && $activeBorrow->due_date && $activeBorrow->due_date->isFuture()
            ? $activeBorrow->due_date->copy()
            : now();
        $estimatedForNextQueue = $activeQueuesCount > 0
            ? $baseDate->copy()->addDays($activeQueuesCount * 7)->format('d M Y')
            : $baseDate->format('d M Y');

        // RIWAYAT PEMINJAMAN: Ambil jejak siapa saja yang pernah baca buku ini
        // PRIVASI: Jangan kirim nama peminjam lain ke member
        $borrowHistory = $book->borrows()
            ->with('user')
            ->orderBy('borrow_date', 'desc')
            ->take(5)
            ->get()
            ->map(function($b) {
                return [
                    'user_name' => 'Member Skarifta', // Anonymized
                    'borrow_date' => $b->borrow_date ? $b->borrow_date->format('d M Y') : '-',
                    'return_date' => $b->return_date ? $b->return_date->format('d M Y') : ($b->status === \App\Models\Borrow::STATUS_ACTIVE ? 'Sedang Dipinjam' : '-'),
                ];
            });

        // Gabungkan ulasan disetujui dengan milik user sendiri jika ada
        $reviews = $book->approvedReviews->map(function($r) {
            return [
                'id' => $r->id,
                'user_name' => $r->user?->name ?? 'Pengguna',
                'rating' => $r->rating,
                'comment' => $r->comment,
                'status' => 'approved',
                'created_at' => $r->created_at->diffForHumans(),
            ];
        });

        if ($userReview) {
            $reviews->prepend([
                'id' => 'user-' . $userReview->id,
                'user_name' => auth()->user()->name,
                'rating' => $userReview->rating,
                'comment' => $userReview->comment,
                'status' => 'pending',
                'created_at' => $userReview->created_at->diffForHumans(),
            ]);
        }

        return [
            'book' => [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'code' => $book->code,
                'isbn' => $book->isbn,
                'pages' => $book->pages,
                'category' => $book->category,
                'rack_code' => $book->rack_code,
                'label_color' => $book->label_color,
                'status' => $book->status,
                'cover_url' => $book->cover_image ? route('books.cover', $book) : null,
                'borrows_count' => $book->borrows_count,
                'avg_rating' => round((float) ($realAvgRating ?? 0), 1),
                'reviews_count' => $book->approved_reviews_count + ($userReview ? 1 : 0),
                'stock_available' => $availableStock,
                'stock_total' => $totalStock,
                'active_queues_count' => $activeQueuesCount,
                'user_queue_id' => $userQueue?->id,
                'user_queue_status' => $userQueue?->status,
                'user_queue_position' => $userQueuePos,
                'user_queue_estimated_date' => $userQueueEstimatedDate,
                'estimated_for_next_queue' => $estimatedForNextQueue,
                'can_review' => $canReview,
                'added_at' => $book->created_at ? $book->created_at->format('d M Y') : '-',
                'borrow_history' => $borrowHistory,
                'reviews' => $reviews,
            ],
            'related' => $relatedBooks
        ];
    }

    public function memberLoans()
    {
        $activeBorrows = Borrow::with(['book', 'summary'])
            ->where('user_id', auth()->id())
            ->whereIn('status', [Borrow::STATUS_ACTIVE, Borrow::STATUS_LATE])
            ->latest('borrow_date')
            ->get();

        $historyBorrows = Borrow::with(['book.reviews' => function($q) {
                $q->where('user_id', auth()->id());
            }, 'summary'])
            ->where('user_id', auth()->id())
            ->where('status', Borrow::STATUS_RETURNED)
            ->latest('return_date')
            ->take(10)
            ->get();

        $myQueues = BookQueue::with('book')
            ->where('user_id', auth()->id())
            ->whereIn('status', [BookQueue::STATUS_WAITING, BookQueue::STATUS_READY, BookQueue::STATUS_CALLED])
            ->latest('created_at')
            ->get();

        return view('member.loans', compact('activeBorrows', 'historyBorrows', 'myQueues'));
    }

    public function submitPunishmentPenalty(Request $request, Borrow $borrow)
    {
        if ($borrow->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'punishment_type' => ['required', 'in:fine,social'],
            'payment_method' => ['nullable', 'required_if:punishment_type,fine', 'in:cash,transfer'],
            'payment_proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'late_reason' => ['nullable', 'string', 'max:1000'],
            'late_evidence' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $proofPath = $borrow->payment_proof;
        if ($request->hasFile('payment_proof')) {
            $proofPath = $request->file('payment_proof')->store('fines/proofs', 'public');
        }

        $evidencePath = $borrow->late_evidence;
        if ($request->hasFile('late_evidence')) {
            $evidencePath = $request->file('late_evidence')->store('fines/evidence', 'public');
        }

        $now = now();
        $lateDays = $borrow->due_date && $now->gt($borrow->due_date)
            ? max(0, \Carbon\Carbon::parse($borrow->due_date)->startOfDay()->diffInDays($now->startOfDay(), false))
            : 0;

        if ($validated['punishment_type'] === Borrow::PUNISHMENT_FINE) {
            $fineAmount = $borrow->fine > 0 ? $borrow->fine : ($lateDays * 15000);
            $paymentStatus = ($validated['payment_method'] ?? '') === Borrow::PAYMENT_TRANSFER && $proofPath
                ? Borrow::PAYMENT_STATUS_PENDING
                : Borrow::PAYMENT_STATUS_UNPAID;

            $borrow->update([
                'punishment_type' => Borrow::PUNISHMENT_FINE,
                'fine' => $fineAmount,
                'fine_type' => $borrow->fine_type ?: 'late',
                'payment_method' => $validated['payment_method'] ?? Borrow::PAYMENT_CASH,
                'payment_proof' => $proofPath,
                'payment_status' => $paymentStatus,
                'late_reason' => $validated['late_reason'] ?? $borrow->late_reason,
                'late_evidence' => $evidencePath,
            ]);
        } else {
            // Hukuman sosial
            $borrow->update([
                'punishment_type' => Borrow::PUNISHMENT_SOCIAL,
                'social_punishment_status' => $borrow->social_punishment_status ?: 'pending_assignment',
                'late_reason' => $validated['late_reason'] ?? $borrow->late_reason,
                'late_evidence' => $evidencePath,
            ]);
        }

        return back()->with('success', 'Konfirmasi sanksi / pembayaran denda berhasil dikirim.');
    }

    public function memberNotifications()
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(20);
        
        // Mark all as read when visiting the page
        auth()->user()->unreadNotifications->markAsRead();

        return view('member.notifications', compact('notifications'));
    }

    public function markNotificationRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return response()->json(['success' => true]);
    }

    public function queue(Book $book)
    {
        $user = auth()->user();
        $userId = $user->id;

        // Batas maksimal 2 antrean buku berbeda per siswa
        $activeQueuesCount = BookQueue::where('user_id', $userId)
            ->whereIn('status', [BookQueue::STATUS_WAITING, BookQueue::STATUS_READY, BookQueue::STATUS_CALLED])
            ->count();

        if ($activeQueuesCount >= 2) {
            return back()->withErrors(['limit' => 'Maksimal booking antrean adalah 2 buku yang berbeda secara bersamaan.']);
        }

        $existing = BookQueue::query()
            ->where('book_id', $book->id)
            ->where('user_id', $userId)
            ->whereIn('status', [
                BookQueue::STATUS_WAITING,
                BookQueue::STATUS_READY,
                BookQueue::STATUS_CALLED,
            ])
            ->first();

        if ($existing) {
            return back()->with('success', 'Kamu sudah masuk antrian/booking untuk buku ini.');
        }

        DB::transaction(function () use ($book, $userId): void {
            $book->refresh();

            $alreadyReserved = BookQueue::query()
                ->where('book_id', $book->id)
                ->whereIn('status', [BookQueue::STATUS_READY, BookQueue::STATUS_CALLED])
                ->exists();

            if ($book->status === Book::STATUS_AVAILABLE && !$alreadyReserved) {
                $queue = BookQueue::query()->create([
                    'user_id' => $userId,
                    'book_id' => $book->id,
                    'status' => BookQueue::STATUS_READY,
                    'ready_at' => now(),
                ]);

                $book->update(['status' => Book::STATUS_RESERVED]);

                $called = app(\App\Services\BookQueueManager::class)->callQueue($queue);
                app(\App\Services\BookQueueNotificationService::class)->notifyCalled($called, 'direct_booking');

                return;
            }

            BookQueue::query()->create([
                'user_id' => $userId,
                'book_id' => $book->id,
                'status' => BookQueue::STATUS_WAITING,
            ]);
        });

        return back()->with('success', 'Permintaan booking/antrean berhasil dikirim.');
    }

    public function cancelQueue(BookQueue $bookQueue, \App\Services\BookQueueManager $queueManager)
    {
        if ($bookQueue->user_id !== auth()->id()) {
            abort(403);
        }

        if (!in_array($bookQueue->status, [BookQueue::STATUS_WAITING, BookQueue::STATUS_READY, BookQueue::STATUS_CALLED], true)) {
            return back()->withErrors(['cancel' => 'Antrean tidak dapat dibatalkan.']);
        }

        $queueManager->cancelQueue($bookQueue);

        return back()->with('success', 'Booking antrean buku berhasil dibatalkan. Estimasi antrean berikutnya telah dimajukan.');
    }

    public function cover(Book $book)
    {
        if (!$book->cover_image || !Storage::disk('public')->exists($book->cover_image)) {
            abort(404);
        }

        return Storage::disk('public')->response($book->cover_image);
    }

    public function printLabel(Book $book)
    {
        $labelSize = request('size', '80x70');
        $qrPayload = $book->code;
        $shortTitle = Str::limit($book->title, 28, '...');
        $displayCode = $book->code . '-' . ($book->exemplar_no ?? 1);
        $qrSvgDataUri = $this->generateQrSvgDataUri($qrPayload);

        $pdf = Pdf::loadView('books.label-pdf', [
            'book' => $book,
            'qrPayload' => $qrPayload,
            'qrSvgDataUri' => $qrSvgDataUri,
            'displayCode' => $displayCode,
            'shortTitle' => $shortTitle,
            'labelSize' => $labelSize,
            'labelWidthMm' => $labelSize === '100x50' ? 100 : 80,
            'labelHeightMm' => $labelSize === '100x50' ? 50 : 70,
        ])->setPaper($this->paperBySize($labelSize), 'landscape');

        return $pdf->download("label-{$book->code}.pdf");
    }

    public function printBulkLabel(Request $request)
    {
        $validated = $request->validate([
            'book_ids' => ['required', 'array', 'min:1'],
            'book_ids.*' => ['integer', 'exists:books,id'],
            'size' => ['nullable', 'in:80x70,100x50'],
        ]);

        $books = Book::whereIn('id', $validated['book_ids'])->orderBy('code')->get();
        $labelSize = $validated['size'] ?? '80x70';
        $labels = [];

        foreach ($books as $book) {
            $qrPayload = $book->code;
            $labels[] = [
                'book' => $book,
                'qrPayload' => $qrPayload,
                'qrSvgDataUri' => $this->generateQrSvgDataUri($qrPayload),
                'displayCode' => $book->code . '-' . ($book->exemplar_no ?? 1),
                'shortTitle' => Str::limit($book->title, 28, '...'),
            ];
        }

        $pdf = Pdf::loadView('books.labels-bulk-pdf', [
            'labels' => $labels,
            'labelSize' => $labelSize,
            'labelWidthMm' => $labelSize === '100x50' ? 100 : 80,
            'labelHeightMm' => $labelSize === '100x50' ? 50 : 70,
        ])->setPaper($this->paperBySize($labelSize), 'landscape');

        return $pdf->download('labels-bulk.pdf');
    }

    private function generateBookCode(): string
    {
        $lastBook = Book::orderByDesc('id')->first();
        $next = $lastBook ? $lastBook->id + 1 : 1;

        do {
            $candidate = 'BK' . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
            $next++;
        } while (Book::where('code', $candidate)->exists());

        return Str::upper($candidate);
    }

    private function paperBySize(string $labelSize): array
    {
        return match ($labelSize) {
            '100x50' => [0, 0, 141.73, 283.46], // base portrait, rendered as landscape
            default => [0, 0, 198.43, 226.77], // base portrait, rendered as landscape
        };
    }

    private function resolveLabelColorByCategory(?string $categoryName): ?string
    {
        if (!$categoryName) {
            return null;
        }

        return BookCategory::where('name', $categoryName)->value('label_color');
    }

    private function generateQrSvgDataUri(string $payload): ?string
    {
        $qrCode = new QrCode(data: $payload, size: 220, margin: 2);
        $writer = new SvgWriter();
        $result = $writer->write($qrCode, null, null, [
            SvgWriter::WRITER_OPTION_EXCLUDE_XML_DECLARATION => true,
        ]);
        $svg = trim($result->getString());

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    public function pendingPostLoanAction()
    {
        $userId = auth()->id();

        // Cari peminjaman yang sudah dikembalikan tapi belum ada review
        $pending = \App\Models\Borrow::with(['book'])
            ->where('user_id', $userId)
            ->where('status', \App\Models\Borrow::STATUS_RETURNED)
            ->whereHas('book', function($q) use ($userId) {
                $q->whereDoesntHave('reviews', function($rq) use ($userId) {
                    $rq->where('user_id', $userId);
                });
            })
            ->latest('return_date')
            ->first();

        if (!$pending) {
            return response()->json(['has_pending' => false]);
        }

        return response()->json([
            'has_pending' => true,
            'borrow_id' => $pending->id,
            'book' => [
                'id' => $pending->book->id,
                'title' => $pending->book->title,
                'author' => $pending->book->author,
                'cover_url' => $pending->book->cover_image ? route('books.cover', $pending->book) : null,
            ]
        ]);
    }

    public function memberLeaderboard()
    {
        // Get Leaderboard: Top 20 student readers only (excluding teachers/pustakawan/admin)
        $leaderboard = \App\Models\User::role('member')
            ->where('member_type', 'student')
            ->withCount(['borrows' => function($q) {
                $q->where('status', \App\Models\Borrow::STATUS_RETURNED);
            }])
            ->orderByDesc('borrows_count')
            ->take(20)
            ->get();

        return view('member.leaderboard', compact('leaderboard'));
    }
}
