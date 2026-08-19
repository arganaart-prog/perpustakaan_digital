<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Borrow extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_LATE = 'late';
    public const STATUS_RETURNED = 'returned';
    public const STATUS_LOST = 'lost';

    public const PUNISHMENT_FINE = 'fine';
    public const PUNISHMENT_SOCIAL = 'social';

    public const PAYMENT_CASH = 'cash';
    public const PAYMENT_TRANSFER = 'transfer';

    public const PAYMENT_STATUS_UNPAID = 'unpaid';
    public const PAYMENT_STATUS_PENDING = 'pending_verification';
    public const PAYMENT_STATUS_PAID = 'paid';

    protected $fillable = [
        'user_id',
        'book_id',
        'borrow_date',
        'due_date',
        'return_date',
        'status',
        'fine',
        'fine_paid_at',
        'punishment_type',
        'fine_type',
        'payment_method',
        'payment_proof',
        'payment_status',
        'late_reason',
        'late_evidence',
        'social_punishment_description',
        'social_punishment_status',
        'social_punishment_completed_at',
    ];

    protected function casts(): array
    {
        return [
            'borrow_date' => 'datetime',
            'due_date' => 'datetime',
            'return_date' => 'datetime',
            'fine_paid_at' => 'datetime',
            'social_punishment_completed_at' => 'datetime',
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

    public function summary(): HasOne
    {
        return $this->hasOne(Summary::class);
    }
}
