<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ActivityLog extends Model
{
    protected $fillable = [
        'account_id',
        'tool_id',
        'waktu',
        'aktivitas',
        'keterangan',
    ];

    protected $casts = [
        'waktu' => 'datetime',
    ];

    // Relasi
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class);
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('waktu', today());
    }

    public function scopeByAccount($query, int $accountId)
    {
        return $query->where('account_id', $accountId);
    }

    public function scopeByTool($query, int $toolId)
    {
        return $query->where('tool_id', $toolId);
    }

    public function scopeByAktivitas($query, string $aktivitas)
    {
        return $query->where('aktivitas', $aktivitas);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('waktu', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ]);
    }

    public function scopeLatest7Days($query)
    {
        return $query->where('waktu', '>=', now()->subDays(7));
    }

    // Accessor untuk format waktu
    public function getFormattedTimeAttribute(): string
    {
        return $this->waktu->format('H:i');
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->waktu->format('d M Y');
    }

    public function getFormattedDateTimeAttribute(): string
    {
        return $this->waktu->format('d M Y H:i');
    }

    // Helper untuk aktivitas color
    public function getAktivitasColorAttribute(): string
    {
        return match($this->aktivitas) {
            'Dipakai' => 'primary',
            'Limit' => 'danger',
            'Reset' => 'success',
            'Login' => 'info',
            'Logout' => 'secondary',
            'Error' => 'warning',
            'Maintenance' => 'dark',
            default => 'secondary',
        };
    }

    // Helper untuk aktivitas icon
    public function getAktivitasIconAttribute(): string
    {
        return match($this->aktivitas) {
            'Dipakai' => 'play-circle',
            'Limit' => 'exclamation-triangle',
            'Reset' => 'arrow-clockwise',
            'Login' => 'box-arrow-in-right',
            'Logout' => 'box-arrow-right',
            'Error' => 'bug',
            'Maintenance' => 'tools',
            default => 'circle',
        };
    }
}
