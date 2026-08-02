<?php

namespace App\Services\ActivityLog;

use App\Models\ActivityLog;
use App\Services\Telegram\TelegramService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ActivityLogService
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }
    public function getAllActivityLogs(): LengthAwarePaginator
    {
        return ActivityLog::with(['account', 'tool'])
            ->latest('waktu')
            ->paginate(20);
    }

    public function getActivityLogById(int $id): ?ActivityLog
    {
        return ActivityLog::with(['account', 'tool'])->find($id);
    }

    public function createActivityLog(array $data): ActivityLog
    {
        $activityLog = ActivityLog::create($data);
        
        // Send notification for Limit or Error status
        $this->checkAndSendNotification($activityLog);
        
        return $activityLog;
    }

    public function updateActivityLog(ActivityLog $activityLog, array $data): bool
    {
        return $activityLog->update($data);
    }

    public function deleteActivityLog(ActivityLog $activityLog): bool
    {
        return $activityLog->delete();
    }

    public function getTodayActivityLogs(): Collection
    {
        return ActivityLog::with(['account', 'tool'])
            ->today()
            ->latest('waktu')
            ->get();
    }

    public function getLatest7DaysLogs(): Collection
    {
        return ActivityLog::with(['account', 'tool'])
            ->latest7Days()
            ->latest('waktu')
            ->get();
    }

    public function getActivityByAccount(int $accountId): LengthAwarePaginator
    {
        return ActivityLog::with(['tool'])
            ->byAccount($accountId)
            ->latest('waktu')
            ->paginate(15);
    }

    public function getActivityByTool(int $toolId): LengthAwarePaginator
    {
        return ActivityLog::with(['account'])
            ->byTool($toolId)
            ->latest('waktu')
            ->paginate(15);
    }

    public function getActivityByDateRange($startDate, $endDate): Collection
    {
        return ActivityLog::with(['account', 'tool'])
            ->dateRange($startDate, $endDate)
            ->latest('waktu')
            ->get();
    }

    public function getActivityStatistics(): array
    {
        return [
            'total_logs' => ActivityLog::count(),
            'today_logs' => ActivityLog::today()->count(),
            'aktivitas_dipakai' => ActivityLog::byAktivitas('Dipakai')->count(),
            'aktivitas_limit' => ActivityLog::byAktivitas('Limit')->count(),
            'aktivitas_reset' => ActivityLog::byAktivitas('Reset')->count(),
            'aktivitas_login' => ActivityLog::byAktivitas('Login')->count(),
            'aktivitas_logout' => ActivityLog::byAktivitas('Logout')->count(),
            'aktivitas_error' => ActivityLog::byAktivitas('Error')->count(),
            'aktivitas_maintenance' => ActivityLog::byAktivitas('Maintenance')->count(),
        ];
    }

    // Quick log methods untuk kemudahan
    public function quickLogDipakai(int $accountId, int $toolId, ?string $keterangan = null): ActivityLog
    {
        return $this->createActivityLog([
            'account_id' => $accountId,
            'tool_id' => $toolId,
            'waktu' => now(),
            'aktivitas' => 'Dipakai',
            'keterangan' => $keterangan,
        ]);
    }

    public function quickLogLimit(int $accountId, int $toolId, ?string $keterangan = null): ActivityLog
    {
        return $this->createActivityLog([
            'account_id' => $accountId,
            'tool_id' => $toolId,
            'waktu' => now(),
            'aktivitas' => 'Limit',
            'keterangan' => $keterangan,
        ]);
    }

    public function quickLogReset(int $accountId, int $toolId, ?string $keterangan = null): ActivityLog
    {
        return $this->createActivityLog([
            'account_id' => $accountId,
            'tool_id' => $toolId,
            'waktu' => now(),
            'aktivitas' => 'Reset',
            'keterangan' => $keterangan,
        ]);
    }

    // Group logs by date for timeline view
    public function getTimelineGroupedByDate(): Collection
    {
        return ActivityLog::with(['account', 'tool'])
            ->latest('waktu')
            ->get()
            ->groupBy(function($log) {
                return $log->waktu->format('Y-m-d');
            });
    }

    // Check and send notification for critical activities
    private function checkAndSendNotification(ActivityLog $activityLog): void
    {
        $account = $activityLog->account;
        $tool = $activityLog->tool;

        // Send notification for Limit status
        if ($activityLog->aktivitas === 'Limit') {
            $message = "🔴 <b>Aktivitas: Limit</b>\n\n";
            $message .= "Akun: <b>{$account->email}</b>\n";
            $message .= "Tool: <b>{$tool->nama}</b>\n";
            $message .= "Waktu: <b>{$activityLog->waktu->format('d/m/Y H:i')}</b>\n";
            if ($activityLog->keterangan) {
                $message .= "Keterangan: {$activityLog->keterangan}\n";
            }

            if (auth()->check()) {
                $this->telegramService->sendNotification(auth()->id(), $message);
            }
        }
        // Send notification for Error status
        elseif ($activityLog->aktivitas === 'Error') {
            $message = "⚠️ <b>Aktivitas: Error</b>\n\n";
            $message .= "Akun: <b>{$account->email}</b>\n";
            $message .= "Tool: <b>{$tool->nama}</b>\n";
            $message .= "Waktu: <b>{$activityLog->waktu->format('d/m/Y H:i')}</b>\n";
            if ($activityLog->keterangan) {
                $message .= "Keterangan: {$activityLog->keterangan}\n";
            }

            if (auth()->check()) {
                $this->telegramService->sendNotification(auth()->id(), $message);
            }
        }
    }
}
