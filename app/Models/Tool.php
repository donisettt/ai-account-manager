<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    protected $fillable = [
        'nama',
        'logo',
        'status_aktif',
        'keterangan',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    public function scopeAktif($query)
    {
        return $query->where('status_aktif', true);
    }

    public function scopeNonAktif($query)
    {
        return $query->where('status_aktif', false);
    }

    // Relasi many-to-many dengan Account
    public function accounts(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'account_tool')
            ->withTimestamps();
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
}
