<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrow;
use App\Models\Summary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SummaryController extends Controller
{
    /**
     * getEligibleBooks: Mengambil daftar buku yang sudah dikembalikan 
     * dan belum pernah dibuatkan rangkumannya oleh user.
     */
    public function getEligibleBooks()
    {
        $userId = Auth::id();

        // Cari semua buku yang pernah dipinjam dan sudah dikembalikan
        $returnedBorrows = Borrow::with('book')
            ->where('user_id', $userId)
            ->where('status', Borrow::STATUS_RETURNED)
            ->get();

        // Ambil ID buku yang sudah punya rangkuman
        $summarizedBookIds = Summary::whereHas('borrow', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->pluck('id'); // Wait, borrow_id di summary, kita butuh book_id dari borrow

        // Koreksi logika: cari book_id yang sudah ada rincian rangkumannya
        $alreadySummarizedBookIds = Borrow::where('user_id', $userId)
            ->whereHas('summary')
            ->pluck('book_id')
            ->unique();

        // Filter: Hanya ambil buku unik yang belum dirangkum
        $eligibleBooks = $returnedBorrows->filter(function($borrow) use ($alreadySummarizedBookIds) {
            return !$alreadySummarizedBookIds->contains($borrow->book_id);
        })->map(function($borrow) {
            return [
                'borrow_id' => $borrow->id,
                'book_id' => $borrow->book_id,
                'title' => $borrow->book->title,
                'author' => $borrow->book->author,
                'cover_url' => $borrow->book->cover_image ? route('books.cover', $borrow->book) : null,
            ];
        })->unique('book_id')->values();

        return response()->json($eligibleBooks);
    }

    public function store(Request $request, Borrow $borrow)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // Max 5MB
            'late_reason' => ['nullable', 'string', 'max:1000'],
            'late_evidence' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        // Keamanan: Pastikan peminjaman ini milik user
        if ($borrow->user_id !== Auth::id()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $summary = $borrow->summary;

        // Validasi: Jika sudah disetujui atau sedang ditinjau, jangan izinkan upload ulang
        if ($summary && in_array($summary->status, ['approved', 'pending'])) {
            return back()->withErrors(['summary' => 'Rangkuman sedang ditinjau atau sudah disetujui.']);
        }

        // Upload file rangkuman
        $filePath = $request->file('file')->store('summaries', 'public');

        // Hitung keterlambatan
        $now = now();
        $targetDate = $borrow->due_date ?? $borrow->borrow_date?->copy()->addDays(7);
        $lateDays = $targetDate && $now->gt($targetDate)
            ? max(0, \Carbon\Carbon::parse($targetDate)->startOfDay()->diffInDays($now->startOfDay(), false))
            : 0;
        $extraPages = $lateDays * 1;

        $evidencePath = null;
        if ($request->hasFile('late_evidence')) {
            $evidencePath = $request->file('late_evidence')->store('summaries/evidence', 'public');
        }

        if ($summary) {
            // Update existing
            $summary->update([
                'file' => $filePath,
                'status' => 'pending',
                'review_note' => null,
                'late_days' => $lateDays,
                'extra_pages_required' => $extraPages,
                'late_reason' => $request->late_reason ?: $summary->late_reason,
                'late_evidence' => $evidencePath ?: $summary->late_evidence,
            ]);
        } else {
            // Create new
            Summary::create([
                'borrow_id' => $borrow->id,
                'file' => $filePath,
                'status' => 'pending',
                'late_days' => $lateDays,
                'extra_pages_required' => $extraPages,
                'late_reason' => $request->late_reason,
                'late_evidence' => $evidencePath,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Rangkuman berhasil dikirim dan akan diperiksa kembali oleh petugas.',
            ]);
        }

        return back()->with('success', 'Rangkuman berhasil dikirim dan akan diperiksa kembali oleh petugas.');
    }

    public function viewFile(Summary $summary)
    {
        if (!Storage::disk('public')->exists($summary->file)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('public')->response($summary->file);
    }
}
