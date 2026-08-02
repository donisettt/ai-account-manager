<?php

namespace App\Services\UsageLog;

use App\Models\UsageLog;
use App\Models\Account;
use App\Models\ActivityLog;
use App\Services\Telegram\TelegramService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UsageLogService
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }
    public function getAllUsageLogs(): LengthAwarePaginator
    {
        return UsageLog::with(['account', 'tool'])
            ->latest('tanggal')
            ->latest('created_at')
            ->paginate(15);
    }

    public function getUsageLogById(int $id): ?UsageLog
    {
        return UsageLog::with(['account', 'tool'])->find($id);
    }

    public function createUsageLog(array $data): UsageLog
    {
        $usageLog = UsageLog::create($data);

        // Update last_used_at di account
        Account::find($data['account_id'])->update([
            'last_used_at' => now()
        ]);

        // Auto-create activity log
        $this->autoCreateActivityLog($usageLog);

        // Send notification if usage > 80%
        $this->checkAndSendNotification($usageLog);

        return $usageLog->load(['account', 'tool']);
    }

    public function updateUsageLog(UsageLog $usageLog, array $data): bool
    {
        $updated = $usageLog->update($data);
        
        // Auto-create activity log jika status berubah
        if (isset($data['status']) && $usageLog->wasChanged('status')) {
            $this->autoCreateActivityLog($usageLog);
        }
        
        // Send notification if usage > 80%
        if (isset($data['limit_used']) || isset($data['limit_total'])) {
            $this->checkAndSendNotification($usageLog);
        }
        
        return $updated;
    }

    public function deleteUsageLog(UsageLog $usageLog): bool
    {
        return $usageLog->delete();
    }

    public function getTodayUsageLogs(): Collection
    {
        return UsageLog::with(['account', 'tool'])
            ->today()
            ->latest('created_at')
            ->get();
    }

    public function getUsageByAccount(int $accountId): LengthAwarePaginator
    {
        return UsageLog::with(['tool'])
            ->byAccount($accountId)
            ->latest('tanggal')
            ->paginate(10);
    }

    public function getUsageByTool(int $toolId): LengthAwarePaginator
    {
        return UsageLog::with(['account'])
            ->byTool($toolId)
            ->latest('tanggal')
            ->paginate(10);
    }

    public function getUsageByDateRange($startDate, $endDate): Collection
    {
        return UsageLog::with(['account', 'tool'])
            ->dateRange($startDate, $endDate)
            ->latest('tanggal')
            ->get();
    }

    public function getUsageStatistics(): array
    {
        return [
            'total_logs' => UsageLog::count(),
            'today_logs' => UsageLog::today()->count(),
            'status_ready' => UsageLog::byStatus('Ready')->count(),
            'status_warning' => UsageLog::byStatus('Warning')->count(),
            'status_limit' => UsageLog::byStatus('Limit')->count(),
            'status_maintenance' => UsageLog::byStatus('Maintenance')->count(),
            'status_in_use' => UsageLog::byStatus('Sedang Dipakai')->count(),
        ];
    }

    // Auto-determine status based on usage percentage
    public function autoDetectStatus(float $limitUsed, float $limitTotal): string
    {
        if ($limitTotal == 0) {
            return 'Maintenance';
        }

        $percentage = ($limitUsed / $limitTotal) * 100;

        return match(true) {
            $percentage >= 95 => 'Limit',
            $percentage >= 70 => 'Warning',
            $percentage > 0 => 'Sedang Dipakai',
            default => 'Ready',
        };
    }

    // Auto-create activity log based on usage status
    private function autoCreateActivityLog(UsageLog $usageLog): void
    {
        $aktivitas = match($usageLog->status) {
            'Ready' => 'Reset',
            'Sedang Dipakai' => 'Dipakai',
            'Limit' => 'Limit',
            'Maintenance' => 'Maintenance',
            default => 'Dipakai',
        };

        ActivityLog::create([
            'account_id' => $usageLog->account_id,
            'tool_id' => $usageLog->tool_id,
            'waktu' => now(),
            'aktivitas' => $aktivitas,
            'keterangan' => "Auto-generated: Status {$usageLog->status} - {$usageLog->limit_used}/{$usageLog->limit_total}",
        ]);
    }

    // Check and send notification if usage exceeds threshold
    private function checkAndSendNotification(UsageLog $usageLog): void
    {
        if ($usageLog->limit_total == 0) {
            return;
        }

        $percentage = ($usageLog->limit_used / $usageLog->limit_total) * 100;
        $account = $usageLog->account;
        $tool = $usageLog->tool;

        // Send notification if usage > 80%
        if ($percentage >= 80 && $percentage < 95) {
            $message = "⚠️ <b>Peringatan Penggunaan</b>\n\n";
            $message .= "Akun: <b>{$account->email}</b>\n";
            $message .= "Tool: <b>{$tool->nama}</b>\n";
            $message .= "Penggunaan: <b>{$usageLog->limit_used}/{$usageLog->limit_total}</b> (" . number_format($percentage, 1) . "%)\n";
            $message .= "Status: 🟡 Warning\n\n";
            $message .= "Limit hampir habis!";

            if (auth()->check()) {
                $this->telegramService->sendNotification(auth()->id(), $message);
            }
        }
        // Send notification if limit reached (>= 95%)
        elseif ($percentage >= 95) {
            $message = "🔴 <b>Limit Tercapai</b>\n\n";
            $message .= "Akun: <b>{$account->email}</b>\n";
            $message .= "Tool: <b>{$tool->nama}</b>\n";
            $message .= "Penggunaan: <b>{$usageLog->limit_used}/{$usageLog->limit_total}</b> (" . number_format($percentage, 1) . "%)\n";
            $message .= "Status: 🔴 Limit\n\n";
            $message .= "Limit sudah habis!";

            if (auth()->check()) {
                $this->telegramService->sendNotification(auth()->id(), $message);
            }
        }
    }
}
