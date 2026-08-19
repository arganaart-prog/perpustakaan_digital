<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserOtpController;
use App\Http\Controllers\Admin\StaffScheduleController;
use App\Http\Controllers\Admin\LabelMasterController;
use App\Http\Controllers\Petugas\MemberApprovalController;
use App\Http\Controllers\Petugas\CirculationController;
use App\Http\Controllers\Member\SummaryController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookReviewController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MasterDataController;
use App\Models\Book;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ADMIN Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Manajemen OTP User
    Route::get('/otp-management', [UserOtpController::class, 'index'])->name('otp.index');
    Route::post('/otp-management/{user}/unlock', [UserOtpController::class, 'unlock'])->name('otp.unlock');
    Route::post('/otp-management/{user}/reset', [UserOtpController::class, 'resetFull'])->name('otp.reset');

    // Jadwal Petugas
    Route::get('/staff-schedule', [StaffScheduleController::class, 'index'])->name('staff.schedule.index');
    Route::put('/staff-schedule/{user}', [StaffScheduleController::class, 'update'])->name('staff.schedule.update');

    // Persetujuan All Users (CRM)
    Route::get('/pending-users', [UserOtpController::class, 'pendingUsers'])->name('pending.users');
    Route::post('/pending-users/{user}/generate-code', [UserOtpController::class, 'generateCode'])->name('users.generate.code');
    Route::post('/pending-users/{user}/reject', [UserOtpController::class, 'rejectUser'])->name('users.reject');
    Route::delete('/users/{user}', [UserOtpController::class, 'destroyUser'])->name('users.destroy');

    // Manajemen Kelas (hanya admin)
    Route::post('/classes', [SchoolClassController::class, 'store'])->name('classes.store');
    Route::delete('/classes/{schoolClass}', [SchoolClassController::class, 'destroy'])->name('classes.destroy');

    // Manajemen Jurusan (hanya admin)
    Route::post('/majors', [MajorController::class, 'store'])->name('majors.store');
    Route::delete('/majors/{major}', [MajorController::class, 'destroy'])->name('majors.destroy');

    // Semua User (Admin)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    // Master Data Kelas & Jurusan
    Route::get('/master-data', [MasterDataController::class, 'index'])->name('master.data.index');
    Route::get('/label-master', [LabelMasterController::class, 'index'])->name('label.master.index');
    Route::post('/label-colors', [LabelMasterController::class, 'storeColor'])->name('label.colors.store');
    Route::delete('/label-colors/{labelColor}', [LabelMasterController::class, 'destroyColor'])->name('label.colors.destroy');
    Route::post('/racks', [LabelMasterController::class, 'storeRack'])->name('racks.store');
    Route::delete('/racks/{rack}', [LabelMasterController::class, 'destroyRack'])->name('racks.destroy');
    Route::post('/book-categories', [LabelMasterController::class, 'storeCategory'])->name('book.categories.store');
    Route::delete('/book-categories/{bookCategory}', [LabelMasterController::class, 'destroyCategory'])->name('book.categories.destroy');

    // Manajemen Buku
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
    Route::get('/books/{book}/label', [BookController::class, 'printLabel'])->name('books.label');
    Route::post('/books/labels/bulk', [BookController::class, 'printBulkLabel'])->name('books.labels.bulk');

    // Detail Buku (akses admin)
    Route::get('/circulation/book/{code}', [CirculationController::class, 'showBookDetail'])->name('circulation.book.detail');

    // Moderasi Ulasan (Admin)
    Route::get('/reviews/moderation', [BookReviewController::class, 'moderation'])->name('reviews.moderation');
    Route::post('/reviews/{review}/approve', [BookReviewController::class, 'approve'])->name('reviews.approve');
    Route::delete('/reviews/{review}', [BookReviewController::class, 'destroy'])->name('reviews.destroy');
});

/*
|--------------------------------------------------------------------------
| PETUGAS Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'role:petugas|admin'])->prefix('petugas')->name('petugas.')->group(function () {

    Route::get('/', [MemberApprovalController::class, 'dashboard'])->name('dashboard');

    // Approval Member
    Route::get('/member-approval', [MemberApprovalController::class, 'index'])->name('member.approval');
    Route::post('/member-approval/{user}/generate-code', [MemberApprovalController::class, 'generateCode'])->name('member.generate.code');
    Route::post('/member-approval/{user}/unlock', [MemberApprovalController::class, 'unlock'])->name('member.unlock');

    // Lihat semua user & Riwayat
    Route::get('/users', [MemberApprovalController::class, 'allUsers'])->name('users');
    Route::get('/users/{user}/history', [MemberApprovalController::class, 'userHistory'])->name('users.history');
    Route::delete('/members/{user}', [MemberApprovalController::class, 'destroyUser'])->name('member.destroy');

    // Manajemen Buku
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
    Route::get('/books/{book}/label', [BookController::class, 'printLabel'])->name('books.label');
    Route::post('/books/labels/bulk', [BookController::class, 'printBulkLabel'])->name('books.labels.bulk');

    // Sirkulasi Peminjaman dan Pengembalian
    Route::get('/circulation/loan', [CirculationController::class, 'loanMode'])->name('circulation.loan');
    Route::post('/circulation/loan', [CirculationController::class, 'processLoan'])->name('circulation.loan.store');
    Route::get('/circulation/scan/{code}', [CirculationController::class, 'scannedBookData'])->name('circulation.scan.book');
    Route::get('/circulation/borrower/search', [CirculationController::class, 'searchBorrower'])->name('circulation.borrower.search');
    Route::get('/circulation/borrower/{user}', [CirculationController::class, 'borrowerSummary'])->name('circulation.borrower.summary');
    Route::get('/circulation/return', [CirculationController::class, 'returnMode'])->name('circulation.return');
    Route::get('/circulation/return/scan/{code}', [CirculationController::class, 'scannedReturnData'])->name('circulation.return.scan.book');
    Route::post('/circulation/return', [CirculationController::class, 'processReturn'])->name('circulation.return.store');
    Route::post('/queues/{bookQueue}/call', [CirculationController::class, 'callQueue'])->name('queues.call');
    Route::post('/queues/{bookQueue}/notify', [CirculationController::class, 'notifyQueue'])->name('queues.notify');
    Route::post('/queues/{bookQueue}/complete', [CirculationController::class, 'completeQueue'])->name('queues.complete');
    Route::get('/circulation/book/{code}', [CirculationController::class, 'showBookDetail'])->name('circulation.book.detail');
    Route::post('/borrows/{borrow}/pay-fine', [CirculationController::class, 'payFine'])->name('borrows.pay-fine');
    Route::post('/borrows/{borrow}/cash-pay', [CirculationController::class, 'confirmCashFine'])->name('borrows.cash-pay');
    Route::post('/borrows/{borrow}/verify-transfer', [CirculationController::class, 'verifyTransferProof'])->name('borrows.verify-transfer');
    Route::post('/borrows/{borrow}/assign-social', [CirculationController::class, 'assignSocialPunishment'])->name('borrows.assign-social');
    Route::post('/borrows/{borrow}/complete-social', [CirculationController::class, 'completeSocialPunishment'])->name('borrows.complete-social');
    Route::get('/borrows/{borrow}/file/{type}', [CirculationController::class, 'viewBorrowFile'])->name('borrows.view-file');
    Route::get('/summaries/moderation', [CirculationController::class, 'summaryModeration'])->name('summaries.moderation');
    Route::post('/summaries/{summary}/approve', [CirculationController::class, 'approveSummary'])->name('summaries.approve');
    Route::post('/summaries/{summary}/reject', [CirculationController::class, 'rejectSummary'])->name('summaries.reject');
    Route::get('/fines', [CirculationController::class, 'fines'])->name('fines.index');

    // Moderasi Ulasan (Petugas)
    Route::get('/reviews/moderation', [BookReviewController::class, 'moderation'])->name('reviews.moderation');
    Route::post('/reviews/{review}/approve', [BookReviewController::class, 'approve'])->name('reviews.approve');
    Route::delete('/reviews/{review}', [BookReviewController::class, 'destroy'])->name('reviews.destroy');

    // Moderasi Foto Profil (Petugas)
    Route::get('/avatars/moderation', [MemberApprovalController::class, 'avatarModeration'])->name('avatars.moderation');
    Route::post('/avatars/{user}/approve', [MemberApprovalController::class, 'approveAvatar'])->name('avatars.approve');
    Route::post('/avatars/{user}/reject', [MemberApprovalController::class, 'rejectAvatar'])->name('avatars.reject');

    // Monitoring Chat Siswa (Petugas & Admin)
    Route::get('/chats/monitoring', [\App\Http\Controllers\ChatController::class, 'monitoringIndex'])->name('chats.monitoring.index');
    Route::get('/chats/monitoring/{conversation}', [\App\Http\Controllers\ChatController::class, 'monitoringShow'])->name('chats.monitoring.show');
    Route::delete('/chats/messages/{message}', [\App\Http\Controllers\ChatController::class, 'deleteMessage'])->name('chats.messages.destroy');
});

/*
|--------------------------------------------------------------------------
| MEMBER / General Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->hasRole('petugas')) {
            return redirect()->route('petugas.dashboard');
        }
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::get('/dashboard', [BookController::class, 'memberDashboard'])->middleware(['auth', 'active', 'role:member'])->name('dashboard');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/books/{book}/cover', [BookController::class, 'cover'])->name('books.cover');
    
    // API Data (Untuk Katalog Mobile & Detail)
    Route::get('/api/books', [BookController::class, 'apiIndex'])->name('api.books.index');
    Route::get('/api/books/{book}', [BookController::class, 'apiDetail'])->name('api.books.detail');
    Route::get('/api/eligible-books-for-summary', [\App\Http\Controllers\SummaryController::class, 'getEligibleBooks'])->name('api.eligible-books-for-summary');
    Route::get('/api/pending-post-loan-action', [BookController::class, 'pendingPostLoanAction'])->name('api.pending-post-loan-action');
    Route::get('/summaries/file/{summary}', [\App\Http\Controllers\SummaryController::class, 'viewFile'])->name('summaries.view-file');
    
    // Debug route
    Route::get('/debug/books-count', function() {
        $count = \App\Models\Book::count();
        $books = \App\Models\Book::limit(5)->get(['id', 'title', 'author', 'status']);
        return response()->json(['count' => $count, 'sample' => $books]);
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/send-otp', [ProfileController::class, 'sendChangePasswordOtp'])->name('profile.send-otp');
    Route::post('/profile/change-password', [ProfileController::class, 'changePasswordWithOtp'])->name('profile.change-password');
    Route::post('/profile/update-details', [ProfileController::class, 'updateDetails'])->name('profile.update-details');
    Route::get('/profile/view/{user}', [ProfileController::class, 'viewPublicProfile'])->name('profile.view');
    Route::get('/users/{user}/avatar', [ProfileController::class, 'avatar'])->name('users.avatar');
});

Route::middleware(['auth', 'active', 'role:member'])->group(function () {
    Route::get('/member/books', [BookController::class, 'memberIndex'])->name('member.books.index');
    Route::get('/member/books/{book}', [BookController::class, 'memberBookDetail'])->name('member.books.detail');
    Route::get('/member/loans', [BookController::class, 'memberLoans'])->name('member.loans');
    Route::get('/member/leaderboard', [BookController::class, 'memberLeaderboard'])->name('member.leaderboard');
    Route::get('/member/notifications', [BookController::class, 'memberNotifications'])->name('member.notifications');
    Route::post('/member/notifications/{id}/read', [BookController::class, 'markNotificationRead'])->name('member.notifications.read');
    Route::post('/member/books/{book}/queue', [BookController::class, 'queue'])->name('member.books.queue');
    Route::post('/member/queues/{bookQueue}/cancel', [BookController::class, 'cancelQueue'])->name('member.queues.cancel');
    Route::post('/member/books/{book}/review', [BookReviewController::class, 'store'])->name('member.books.review');
    Route::post('/member/borrows/{borrow}/summary', [SummaryController::class, 'store'])->name('member.summary.store');
    Route::post('/member/borrows/{borrow}/punishment', [BookController::class, 'submitPunishmentPenalty'])->name('member.borrows.punishment');

    // Chat Antar Siswa
    Route::get('/member/chats', [\App\Http\Controllers\ChatController::class, 'index'])->name('member.chats.index');
    Route::get('/member/chats/start/{targetUser}', [\App\Http\Controllers\ChatController::class, 'startWithUser'])->name('member.chats.start');
    Route::get('/member/chats/{conversation}', [\App\Http\Controllers\ChatController::class, 'show'])->name('member.chats.show');
    Route::post('/member/chats/{conversation}/send', [\App\Http\Controllers\ChatController::class, 'sendMessage'])->name('member.chats.send');
    Route::get('/member/chats/{conversation}/fetch', [\App\Http\Controllers\ChatController::class, 'fetchMessages'])->name('member.chats.fetch');
});

require __DIR__.'/auth.php';
