<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookQueue extends Model
{
    use HasFactory;

    public const STATUS_WAITING = 'waiting';
    public const STATUS_READY = 'ready';
    public const STATUS_CALLED = 'called';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_DONE = 'done';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'book_id',
        'status',
        'ready_at',
        'called_at',
        'deadline',
        'notified_at',
        'unclaimed_email_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'ready_at' => 'datetime',
            'called_at' => 'datetime',
            'deadline' => 'datetime',
            'notified_at' => 'datetime',
            'unclaimed_email_sent_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get queue position (1-based index) among waiting users for the same book.
     */
    public function getQueuePosition(): int
    {
        if ($this->status === self::STATUS_READY || $this->status === self::STATUS_CALLED) {
            return 1;
        }

        $aheadCount = self::query()
            ->where('book_id', $this->book_id)
            ->whereIn('status', [self::STATUS_WAITING, self::STATUS_READY, self::STATUS_CALLED])
            ->where('created_at', '<', $this->created_at)
            ->count();

        return $aheadCount + 1;
    }

    /**
     * Calculate estimated availability date.
     */
    public function getEstimatedAvailableDate(): Carbon
    {
        $position = $this->getQueuePosition();

        // Check active borrow on this book
        $activeBorrow = Borrow::where('book_id', $this->book_id)
            ->whereIn('status', [Borrow::STATUS_ACTIVE, Borrow::STATUS_LATE])
            ->latest('due_date')
            ->first();

        $baseDate = $activeBorrow && $activeBorrow->due_date && $activeBorrow->due_date->isFuture()
            ? $activeBorrow->due_date->copy()
            : now();

        if ($position <= 1) {
            return $baseDate;
        }

        // Each queue ahead gets standard 7 days loan duration
        return $baseDate->copy()->addDays(($position - 1) * 7);
    }
}
