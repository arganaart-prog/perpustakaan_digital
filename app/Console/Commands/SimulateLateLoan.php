<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\Borrow;
use App\Models\User;
use Illuminate\Console\Command;

class SimulateLateLoan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loan:simulate-late {userId?} {daysLate=2}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulasi peminjaman terlambat untuk kebutuhan pengetesan denda dan hukuman sosial';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('userId');
        $daysLate = (int) $this->argument('daysLate');

        $user = $userId ? User::find($userId) : User::where('email', 'like', '%ghozy%')->orWhere('id', 25)->first() ?? User::role('member')->first();

        if (!$user) {
            $this->error('User member tidak ditemukan.');
            return 1;
        }

        $book = Book::first();
        if (!$book) {
            $this->error('Buku tidak ditemukan di database.');
            return 1;
        }

        $borrow = Borrow::where('user_id', $user->id)->whereIn('status', ['active', 'late'])->first();

        $borrowDate = now()->subDays($daysLate + 7);
        $dueDate = now()->subDays($daysLate);
        $fineAmount = $daysLate * 15000;

        if ($borrow) {
            $borrow->update([
                'borrow_date' => $borrowDate,
                'due_date' => $dueDate,
                'status' => Borrow::STATUS_LATE,
                'fine' => $fineAmount,
                'punishment_type' => null,
                'payment_method' => null,
                'payment_status' => Borrow::PAYMENT_STATUS_UNPAID,
            ]);
            $this->info("✓ Berhasil mengubah peminjaman aktif ID #{$borrow->id} milik {$user->name} menjadi terlambat {$daysLate} hari (Denda: Rp " . number_format($fineAmount, 0, ',', '.') . ").");
        } else {
            $borrow = Borrow::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'borrow_date' => $borrowDate,
                'due_date' => $dueDate,
                'status' => Borrow::STATUS_LATE,
                'fine' => $fineAmount,
                'punishment_type' => null,
                'payment_method' => null,
                'payment_status' => Borrow::PAYMENT_STATUS_UNPAID,
            ]);
            $this->info("✓ Berhasil membuat transaksi peminjaman baru untuk {$user->name} buku '{$book->title}' yang terlambat {$daysLate} hari (Denda: Rp " . number_format($fineAmount, 0, ',', '.') . ").");
        }

        return 0;
    }
}
