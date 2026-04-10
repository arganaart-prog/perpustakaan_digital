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

class BookController extends Controller
{
    public function index(Request $request)
    {
        $books = Book::orderBy('title')->paginate(10);
        $routePrefix = $request->routeIs('admin.*') ? 'admin' : 'petugas';
        $panelTitle = $routePrefix === 'admin' ? 'Manajemen Buku (Admin)' : 'Manajemen Buku (Petugas)';
        $labelColors = LabelColor::orderBy('name')->get();
        $racks = Rack::orderBy('code')->get();
        $categories = BookCategory::orderBy('name')->get();

        return view('books.manage', compact('books', 'routePrefix', 'panelTitle', 'labelColors', 'racks', 'categories'));
    }

    public function store(Request $request)
    {
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

        $validated['code'] = $this->generateBookCode();
        $validated['exemplar_no'] = (int) ($validated['exemplar_no'] ?? 1);
        $validated['label_color'] = $this->resolveLabelColorByCategory($validated['category'] ?? null);

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

    public function memberIndex()
    {
        $books = Book::orderBy('title')->paginate(10);
        $activeBorrows = Borrow::with(['book', 'summary'])
            ->where('user_id', auth()->id())
            ->whereIn('status', [Borrow::STATUS_ACTIVE, Borrow::STATUS_LATE])
            ->latest('borrow_date')
            ->get();

        $myQueues = BookQueue::query()
            ->where('user_id', auth()->id())
            ->whereIn('status', [
                BookQueue::STATUS_WAITING,
                BookQueue::STATUS_READY,
                BookQueue::STATUS_CALLED,
            ])
            ->orderByDesc('id')
            ->get()
            ->keyBy('book_id');

        return view('books.member-index', compact('books', 'activeBorrows', 'myQueues'));
    }

    public function queue(Book $book)
    {
        $userId = auth()->id();

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
                BookQueue::query()->create([
                    'user_id' => $userId,
                    'book_id' => $book->id,
                    'status' => BookQueue::STATUS_READY,
                    'ready_at' => now(),
                ]);

                $book->update(['status' => Book::STATUS_RESERVED]);

                return;
            }

            BookQueue::query()->create([
                'user_id' => $userId,
                'book_id' => $book->id,
                'status' => BookQueue::STATUS_WAITING,
            ]);
        });

        return back()->with('success', 'Permintaan booking/antrian berhasil dikirim.');
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
}
