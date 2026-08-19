<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Book extends Model
{
    use HasFactory;

    public const STATUS_AVAILABLE = 'available';
    public const STATUS_BORROWED = 'borrowed';
    public const STATUS_RESERVED = 'reserved';
    public const STATUS_LOST = 'lost';

    protected $fillable = [
        'code',
        'title',
        'author',
        'isbn',
        'pages',
        'cover_image',
        'category',
        'rack_code',
        'label_color',
        'exemplar_no',
        'status',
    ];

    public function queues(): HasMany
    {
        return $this->hasMany(BookQueue::class);
    }

    public function borrows(): HasMany
    {
        return $this->hasMany(Borrow::class);
    }

    public function bookQueues(): HasMany
    {
        return $this->hasMany(BookQueue::class);
    }

    public function activeBorrow(): HasOne
    {
        return $this->hasOne(Borrow::class)
            ->whereIn('status', [Borrow::STATUS_ACTIVE, Borrow::STATUS_LATE]);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(BookReview::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(BookReview::class)->where('is_approved', true)->latest();
    }
}
