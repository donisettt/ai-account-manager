<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsageLog extends Model
{
    protected $fillable = [
        'account_id',
        'tool_id',
        'tanggal',
        'limit_used',
        'limit_total',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'limit_used' => 'decimal:2',
        'limit_total' => 'decimal:2',
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
        return $query->whereDate('tanggal', today());
    }

    public function scopeByAccount($query, int $accountId)
    {
        return $query->where('account_id', $accountId);
    }

    public function scopeByTool($query, int $toolId)
    {
        return $query->where('tool_id', $toolId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    // Accessor untuk persentase penggunaan
    public function getUsagePercentageAttribute(): float
    {
        if ($this->limit_total == 0) {
            return 0;
        }
        return ($this->limit_used / $this->limit_total) * 100;
    }

    // Accessor untuk sisa limit
    public function getRemainingLimitAttribute(): float
    {
        return $this->limit_total - $this->limit_used;
    }

    // Helper untuk status color
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'Ready' => 'success',
            'Warning' => 'warning',
            'Limit' => 'danger',
            'Maintenance' => 'dark',
            'Sedang Dipakai' => 'primary',
            default => 'secondary',
        };
    }

    // Helper untuk status emoji
    public function getStatusEmojiAttribute(): string
    {
        return match($this->status) {
            'Ready' => '🟢',
            'Warning' => '🟡',
            'Limit' => '🔴',
            'Maintenance' => '⚫',
            'Sedang Dipakai' => '🔵',
            default => '⚪',
        };
    }
}
