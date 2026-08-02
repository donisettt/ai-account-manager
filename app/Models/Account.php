<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Crypt;

class Account extends Model
{
    protected $fillable = [
        'email',
        'password',
        'provider',
        'provider_login',
        'recovery_email',
        'status',
        'last_used_at',
        'catatan',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    // Accessor untuk decrypt password
    public function getDecryptedPasswordAttribute(): ?string
    {
        try {
            return $this->password ? Crypt::decryptString($this->password) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    // Mutator untuk encrypt password
    public function setPasswordAttribute($value): void
    {
        if ($value) {
            $this->attributes['password'] = Crypt::encryptString($value);
        }
    }

    // Relasi many-to-many dengan Tool
    public function tools(): BelongsToMany
    {
        return $this->belongsToMany(Tool::class, 'account_tool')
            ->withTimestamps();
    }

    // Scopes
    public function scopeReady($query)
    {
        return $query->where('status', 'Ready');
    }

    public function scopeInUse($query)
    {
        return $query->where('status', 'In Use');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'Suspended');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'Expired');
    }

    public function scopeByProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    // Relasi dengan UsageLog
    public function usageLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UsageLog::class);
    }

    // Relasi dengan ActivityLog
    public function activityLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Get latest usage log
    public function getLatestUsageAttribute()
    {
        return $this->usageLogs()->latest('tanggal')->first();
    }

    // Get latest activity
    public function getLatestActivityAttribute()
    {
        return $this->activityLogs()->latest('waktu')->first();
    }
}
