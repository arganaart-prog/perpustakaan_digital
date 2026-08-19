<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChatConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_one_id',
        'user_two_id',
        'starter_id',
        'is_accepted',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'is_accepted' => 'boolean',
            'last_message_at' => 'datetime',
        ];
    }

    public function userOne(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    public function starter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'starter_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id')->orderBy('created_at', 'asc');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(ChatMessage::class, 'conversation_id')->latestOfMany();
    }

    public function getOtherUser(User $currentUser): User
    {
        return $this->user_one_id === $currentUser->id ? $this->userTwo : $this->userOne;
    }

    public function canSendMessage(User $currentUser): bool
    {
        if ($this->is_accepted) {
            return true;
        }

        // If conversation is not accepted yet:
        // Starter cannot send if they already sent the intro message
        if ($this->starter_id === $currentUser->id) {
            $messageCount = $this->messages()->where('sender_id', $currentUser->id)->count();
            return $messageCount === 0;
        }

        // Receiver can reply
        return true;
    }
}
