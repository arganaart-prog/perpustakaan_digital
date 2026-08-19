<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'whatsapp_number',
        'password',
        'member_type',
        'otp',
        'otp_expired_at',
        'otp_attempt',
        'otp_next_allowed_at',
        'otp_unlocked',
        'is_verified',
        // Approval system fields
        'status',
        'code_attempt',
        'pending_expired_at',
        'approved_by',
        'approved_at',
        'work_days',
        'is_visible',
        'google_id',
        // Student profile fields
        'kelas',
        'jurusan',
        // Profile Customization & Avatar approval fields
        'avatar',
        'avatar_pending',
        'bio',
        'social_links',
        'instagram',
        'facebook',
        'twitter',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'   => 'datetime',
            'otp_expired_at'      => 'datetime',
            'otp_next_allowed_at' => 'datetime',
            'pending_expired_at'  => 'datetime',
            'approved_at'         => 'datetime',
            'password'            => 'hashed',
            'is_verified'         => 'boolean',
            'otp_unlocked'        => 'boolean',
            'is_visible'          => 'boolean',
            'social_links'        => 'array',
        ];
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function activationCode()
    {
        return $this->hasOne(ActivationCode::class, 'user_id');
    }

    public function passwordOtps()
    {
        return $this->hasMany(PasswordOtp::class);
    }

    public function bookQueues(): HasMany
    {
        return $this->hasMany(BookQueue::class);
    }

    public function borrows(): HasMany
    {
        return $this->hasMany(Borrow::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->avatar)) {
            return route('users.avatar', $this);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=047857&background=ecfdf5&size=256';
    }

    public function getPendingAvatarUrlAttribute(): ?string
    {
        if ($this->avatar_pending && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->avatar_pending)) {
            return route('users.avatar', ['user' => $this->id, 'pending' => 1]);
        }

        return null;
    }
}
