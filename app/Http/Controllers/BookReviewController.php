<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookReviewController extends Controller
{
    /**
     * store: Menyimpan ulasan baru dari member.
     * Status default: Belum disetujui (is_approved = false).
     */
    public function store(Request $request, Book $book)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        // Cek apakah user pernah meminjam buku ini dan sudah mengembalikannya
        $hasBorrowed = \App\Models\Borrow::where('user_id', Auth::id())
            ->where('book_id', $book->id)
            ->where('status', \App\Models\Borrow::STATUS_RETURNED)
            ->exists();

        if (!$hasBorrowed) {
            return back()->withErrors(['review' => 'Anda hanya dapat memberikan ulasan pada buku yang pernah Anda pinjam dan sudah dikembalikan.']);
        }

        BookReview::create([
            'user_id' => Auth::id(),
            'book_id' => $book->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'is_approved' => false, // Harus dimoderasi
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ulasan Anda telah terkirim! Terima kasih.',
            ]);
        }

        return back()->with('success', 'Ulasan Anda telah terkirim! Ingin mengumpulkan rangkuman untuk buku ini? Klik menu "Kumpulkan Rangkuman" di Dashboard.');
    }

    /**
     * index: Menampilkan daftar ulasan yang membutuhkan moderasi.
     * Diakses oleh Admin atau Petugas.
     */
    public function moderation()
    {
        $pendingReviews = BookReview::with(['user', 'book'])
            ->where('is_approved', false)
            ->latest()
            ->paginate(15);

        $routePrefix = Auth::user()->hasRole('admin') ? 'admin' : 'petugas';

        return view('admin.reviews.moderation', compact('pendingReviews', 'routePrefix'));
    }

    /**
     * approve: Menyetujui ulasan agar tampil di katalog publik.
     */
    public function approve(BookReview $review)
    {
        $review->update([
            'is_approved' => true,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Ulasan berhasil disetujui.');
    }

    /**
     * destroy: Menolak/Menghapus ulasan.
     */
    public function destroy(BookReview $review)
    {
        $review->delete();
        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}
