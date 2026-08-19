<?php

namespace App\Console\Commands;

use App\Mail\UnclaimedBookReminderMail;
use App\Models\BookQueue;
use App\Services\BookQueueManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckUnclaimedQueues extends Command
{
    protected $signature = 'queue:check-unclaimed';
    protected $description = 'Kirim email pengingat 24 jam dan proses pemindahan otomatis buku antrean yang lewat 2 hari';

    public function handle(BookQueueManager $manager)
    {
        $now = now();

        // 1. Kirim Email Pengingat 24 Jam
        $unclaimed24h = BookQueue::with(['user', 'book'])
            ->whereIn('status', [BookQueue::STATUS_READY, BookQueue::STATUS_CALLED])
            ->whereNull('unclaimed_email_sent_at')
            ->where(function ($q) use ($now) {
                $cutoff = $now->copy()->subHours(24);
                $q->where('called_at', '<=', $cutoff)
                  ->orWhere('ready_at', '<=', $cutoff);
            })
            ->get();

        foreach ($unclaimed24h as $queue) {
            if (!empty($queue->user?->email)) {
                try {
                    Mail::to($queue->user->email)->send(new UnclaimedBookReminderMail($queue));
                    $this->info("Email pengingat 24 jam terkirim ke {$queue->user->email} untuk buku '{$queue->book->title}'");
                } catch (\Throwable $e) {
                    Log::warning("Gagal kirim email reminder 24 jam: " . $e->getMessage());
                }
            }

            $queue->update([
                'unclaimed_email_sent_at' => $now,
            ]);
        }

        // 2. Auto-Call antrean yang baru ready
        $manager->autoCallReadyQueues($now);

        // 3. Expire antrean yang lewat 2 hari (48 jam) & Otomatis Pindahkan ke Siswa Berikutnya
        $expiredQueues = $manager->expireOverdueQueues($now);
        $this->info("Selesai. Total {$unclaimed24h->count()} email pengingat 24h terkirim, {$expiredQueues->count()} antrean kadaluarsa dipindahkan.");

        return 0;
    }
}
