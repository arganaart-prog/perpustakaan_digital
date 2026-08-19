<?php

namespace App\Console\Commands;

use App\Models\BookQueue;
use App\Services\BookQueueManager;
use App\Services\BookQueueNotificationService;
use Illuminate\Console\Command;

class AutoCallReadyUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-call-ready-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis memanggil member yang bukunya sudah siap lebih dari 2 jam';

    /**
     * Execute the console command.
     */
    public function handle(BookQueueManager $manager, BookQueueNotificationService $notifier)
    {
        $this->info('Memulai pengecekan antrian READY (Threshold 2 jam)...');

        // Cari antrian READY yang sudah lewat 120 menit (2 jam)
        $threshold = now()->subMinutes(120);

        $readyQueues = BookQueue::where('status', BookQueue::STATUS_READY)
            ->where('ready_at', '<=', $threshold)
            ->get();

        if ($readyQueues->isEmpty()) {
            $this->info('Tidak ada antrian yang perlu dipanggil otomatis.');
            return 0;
        }

        foreach ($readyQueues as $queue) {
            $this->info("Otomatis memanggil User: {$queue->user->name} untuk Buku: {$queue->book->title}");
            
            // 1. Ubah status ke CALLED & tentukan deadline
            $manager->callQueue($queue);

            // 2. Kirim Notifikasi (Email & Log)
            $notifier->notifyCalled($queue, 'auto_cli');
        }

        $this->info('Proses selesai.');
        return 0;
    }
}
