<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    protected $fillable = ['name', 'mobile', 'last_synced_at', 'last_read_at', 'locked_by_user_id', 'locked_at'];

    protected $casts = [
        'last_synced_at' => 'datetime',
        'last_read_at' => 'datetime',
        'locked_at' => 'datetime',
    ];

    public function lockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by_user_id');
    }

    public function isLockExpired(): bool
    {
        if (!$this->locked_at) return true;
        $ttl = (int) config('chat.lock_ttl_seconds', 120);
        return $this->locked_at->diffInSeconds(now()) > $ttl;
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany('sent_at');
    }
}
