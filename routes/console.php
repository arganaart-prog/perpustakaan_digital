<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\BookQueueManager;
use App\Services\BookQueueNotificationService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('library:process-book-queues', function (BookQueueManager $manager, BookQueueNotificationService $notifier) {
    $autoCalled = $manager->autoCallReadyQueues();
    $expired = $manager->expireOverdueQueues();

    $autoCalled->each(function ($queue) use ($notifier) {
        $notifier->notifyCalled($queue, 'auto');
    });

    $this->info("Auto called: {$autoCalled->count()}");
    $this->info("Expired: {$expired->count()}");
})->purpose('Process ready and overdue book queues');

Schedule::command('library:process-book-queues')->everyMinute();
Schedule::command('app:auto-call-ready-users')->everyMinute();
