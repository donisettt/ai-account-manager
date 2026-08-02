<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TelegramUser extends Model
{
    protected $fillable = [
        'telegram_id',
        'username',
        'chat_id',
        'first_name',
        'last_name',
        'is_active',
        'notifications_enabled',
        'user_id',
        'auth_token',
        'token_expires_at',
        'last_command_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'notifications_enabled' => 'boolean',
        'token_expires_at' => 'datetime',
        'last_command_at' => 'datetime',
    ];

    // Relasi
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Check if authenticated
    public function isAuthenticated(): bool
    {
        return $this->user_id !== null;
    }

    // Check if token valid
    public function isTokenValid(): bool
    {
        if (!$this->auth_token || !$this->token_expires_at) {
            return false;
        }
        
        return $this->token_expires_at->isFuture();
    }

    // Generate new auth token
    public function generateAuthToken(): string
    {
        $this->auth_token = strtoupper(substr(md5(uniqid()), 0, 8));
        $this->token_expires_at = now()->addHours(24);
        $this->save();
        
        return $this->auth_token;
    }

    // Clear authentication
    public function clearAuth(): void
    {
        $this->user_id = null;
        $this->auth_token = null;
        $this->token_expires_at = null;
        $this->save();
    }

    // Update last command timestamp
    public function updateLastCommand(): void
    {
        $this->last_command_at = now();
        $this->save();
    }

    // Scope
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAuthenticated($query)
    {
        return $query->whereNotNull('user_id');
    }

    public function scopeWithNotifications($query)
    {
        return $query->where('notifications_enabled', true);
    }
}
