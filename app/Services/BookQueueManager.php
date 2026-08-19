<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookQueue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BookQueueManager
{
    public function __construct(
        protected BookQueueDeadlineCalculator $deadlineCalculator,
    ) {
    }

    public function markNextQueueReady(Book $book, ?Carbon $readyAt = null): ?BookQueue
    {
        $readyAt ??= now();

        return DB::transaction(function () use ($book, $readyAt) {
            $queue = BookQueue::query()
                ->where('book_id', $book->id)
                ->where('status', BookQueue::STATUS_WAITING)
                ->orderBy('created_at')
                ->lockForUpdate()
                ->first();

            if (!$queue) {
                // If no waiting queues, book becomes available
                $book->update(['status' => Book::STATUS_AVAILABLE]);
                return null;
            }

            $queue->update([
                'status' => BookQueue::STATUS_READY,
                'ready_at' => $readyAt,
            ]);

            $book->update(['status' => Book::STATUS_RESERVED]);

            return $queue->fresh();
        });
    }

    public function callQueue(BookQueue $queue, ?Carbon $calledAt = null): BookQueue
    {
        $calledAt ??= now();

        $queue->update([
            'status' => BookQueue::STATUS_CALLED,
            'called_at' => $calledAt,
            'deadline' => $this->deadlineCalculator->calculate($calledAt),
        ]);

        return $queue->fresh();
    }

    public function autoCallReadyQueues(?Carbon $now = null): Collection
    {
        $now ??= now();
        $threshold = $now->copy()->subMinutes((int) config('library_queue.auto_call_after_minutes', 0));

        return BookQueue::query()
            ->where('status', BookQueue::STATUS_READY)
            ->whereNotNull('ready_at')
            ->where('ready_at', '<=', $threshold)
            ->orderBy('ready_at')
            ->get()
            ->map(function (BookQueue $queue) use ($now) {
                $called = $this->callQueue($queue, $now);
                app(BookQueueNotificationService::class)->notifyCalled($called, 'auto_call');
                return $called;
            });
    }

    public function expireOverdueQueues(?Carbon $now = null): Collection
    {
        $now ??= now();

        return BookQueue::query()
            ->where('status', BookQueue::STATUS_CALLED)
            ->whereNotNull('deadline')
            ->where('deadline', '<=', $now)
            ->orderBy('deadline')
            ->get()
            ->map(function (BookQueue $queue) use ($now) {
                $queue->update([
                    'status' => BookQueue::STATUS_EXPIRED,
                ]);

                // Automatically advance to the next person in queue!
                $book = $queue->book;
                $nextQueue = $this->markNextQueueReady($book, $now);
                if ($nextQueue) {
                    $calledNext = $this->callQueue($nextQueue, $now);
                    app(BookQueueNotificationService::class)->notifyCalled($calledNext, 'auto_transfer');
                }

                return $queue->fresh();
            });
    }

    /**
     * Cancel a queue and advance next student if it was ready/called.
     */
    public function cancelQueue(BookQueue $queue): void
    {
        $wasActive = in_array($queue->status, [BookQueue::STATUS_READY, BookQueue::STATUS_CALLED], true);
        $book = $queue->book;

        $queue->update([
            'status' => BookQueue::STATUS_CANCELLED,
        ]);

        if ($wasActive && $book) {
            $nextQueue = $this->markNextQueueReady($book);
            if ($nextQueue) {
                $calledNext = $this->callQueue($nextQueue);
                app(BookQueueNotificationService::class)->notifyCalled($calledNext, 'cancelled_transfer');
            }
        }
    }
}
