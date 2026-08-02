<?php

namespace App\Services\Telegram;

use App\Models\{TelegramUser, Account, Tool, UsageLog, ActivityLog};
use App\Services\{UsageLog\UsageLogService, ActivityLog\ActivityLogService};
use Telegram\Bot\Objects\Update;

class CommandHandler
{
    public function __construct(
        private TelegramService $telegramService,
        private MessageFormatter $formatter,
        private UsageLogService $usageLogService,
        private ActivityLogService $activityLogService
    ) {}

    public function handle(Update $update, TelegramUser $telegramUser): void
    {
        try {
            $message = $update->getMessage();
            $text = $message->getText();
            $chatId = $message->getChat()->getId();

            \Log::info('Handling command', [
                'text' => $text,
                'chat_id' => $chatId,
                'user_authenticated' => $telegramUser->isAuthenticated()
            ]);

            // Update last command
            $telegramUser->updateLastCommand();

            // Parse command
            $command = $this->parseCommand($text);
            
            \Log::info('Parsed command', $command);

            // Handle command
            match($command['action']) {
                'start' => $this->handleStart($chatId, $telegramUser),
                'help' => $this->handleHelp($chatId),
                'auth' => $this->handleAuth($chatId, $telegramUser, $command['params']),
                'logout' => $this->handleLogout($chatId, $telegramUser),
                'status' => $this->handleStatus($chatId, $telegramUser),
                'accounts' => $this->handleAccounts($chatId, $telegramUser),
                'account' => $this->handleAccount($chatId, $telegramUser, $command['params']),
                'usage' => $this->handleUsage($chatId, $telegramUser),
                'activity' => $this->handleActivity($chatId, $telegramUser),
                'log' => $this->handleLog($chatId, $telegramUser, $command['params']),
                'quick' => $this->handleQuick($chatId, $telegramUser, $command['params']),
                'update' => $this->handleUpdate($chatId, $telegramUser, $command['params']),
                'reset' => $this->handleReset($chatId, $telegramUser, $command['params']),
                'notify' => $this->handleNotify($chatId, $telegramUser, $command['params']),
                default => $this->handleUnknown($chatId),
            };
            
            \Log::info('Command handled successfully');
        } catch (\Exception $e) {
            \Log::error('Command handler error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Send error message to user
            $this->telegramService->sendMessage(
                $message->getChat()->getId(),
                "❌ Terjadi kesalahan saat memproses command.\n\nError: " . $e->getMessage()
            );
        }
    }

    private function parseCommand(string $text): array
    {
        $parts = explode(' ', trim($text));
        $command = str_replace('/', '', strtolower($parts[0]));
        $params = array_slice($parts, 1);

        return [
            'action' => $command,
            'params' => $params,
        ];
    }

    private function requireAuth(int $chatId, TelegramUser $telegramUser): bool
    {
        if (!$telegramUser->isAuthenticated()) {
            $this->telegramService->sendMessage(
                $chatId,
                "❌ Anda belum terautentikasi.\n\n" .
                "Silakan login di web app dan gunakan command:\n" .
                "<code>/auth TOKEN</code>"
            );
            return false;
        }
        return true;
    }

    private function handleStart(int $chatId, TelegramUser $telegramUser): void
    {
        $message = "👋 <b>Selamat datang di AI Account Manager Bot!</b>\n\n";
        
        if ($telegramUser->isAuthenticated()) {
            $message .= "✅ Anda sudah terautentikasi sebagai: <b>{$telegramUser->user->name}</b>\n\n";
            $message .= "Gunakan /help untuk melihat daftar command.";
        } else {
            $message .= "Untuk memulai, silakan autentikasi terlebih dahulu:\n\n";
            $message .= "1. Login ke web app\n";
            $message .= "2. Buka menu Telegram Bot Settings\n";
            $message .= "3. Generate token\n";
            $message .= "4. Gunakan command: <code>/auth TOKEN</code>\n\n";
            $message .= "Setelah terautentikasi, Anda dapat:\n";
            $message .= "• Monitoring status akun\n";
            $message .= "• Update penggunaan\n";
            $message .= "• Log aktivitas\n";
            $message .= "• Terima notifikasi";
        }

        $this->telegramService->sendMessage($chatId, $message);
    }

    private function handleHelp(int $chatId): void
    {
        $message = $this->formatter->formatHelp();
        $this->telegramService->sendMessage($chatId, $message);
    }

    private function handleAuth(int $chatId, TelegramUser $telegramUser, array $params): void
    {
        if (empty($params[0])) {
            $this->telegramService->sendMessage($chatId, "❌ Format salah. Gunakan: <code>/auth TOKEN</code>");
            return;
        }

        $token = strtoupper($params[0]);
        $success = $this->telegramService->linkUserToTelegram($token, $telegramUser->telegram_id);

        if ($success) {
            $telegramUser->refresh();
            $this->telegramService->sendMessage(
                $chatId,
                "✅ <b>Autentikasi berhasil!</b>\n\n" .
                "Anda sekarang terhubung sebagai: <b>{$telegramUser->user->name}</b>\n\n" .
                "Gunakan /help untuk melihat daftar command."
            );
        } else {
            $this->telegramService->sendMessage(
                $chatId,
                "❌ <b>Token tidak valid atau sudah expired.</b>\n\n" .
                "Silakan generate token baru di web app."
            );
        }
    }

    private function handleLogout(int $chatId, TelegramUser $telegramUser): void
    {
        if (!$this->requireAuth($chatId, $telegramUser)) return;

        $telegramUser->clearAuth();
        $this->telegramService->sendMessage(
            $chatId,
            "✅ <b>Logout berhasil!</b>\n\n" .
            "Gunakan /auth untuk login kembali."
        );
    }

    private function handleStatus(int $chatId, TelegramUser $telegramUser): void
    {
        if (!$this->requireAuth($chatId, $telegramUser)) return;

        $accounts = Account::with(['tools', 'usageLogs' => function($q) {
            $q->latest('tanggal')->limit(1);
        }])->get();

        $message = $this->formatter->formatStatus($accounts);
        $this->telegramService->sendMessage($chatId, $message);
    }

    private function handleAccounts(int $chatId, TelegramUser $telegramUser): void
    {
        if (!$this->requireAuth($chatId, $telegramUser)) return;

        $accounts = Account::with('tools')->get();
        $message = $this->formatter->formatAccounts($accounts);
        $this->telegramService->sendMessage($chatId, $message);
    }

    private function handleAccount(int $chatId, TelegramUser $telegramUser, array $params): void
    {
        if (!$this->requireAuth($chatId, $telegramUser)) return;

        if (empty($params[0])) {
            $this->telegramService->sendMessage($chatId, "❌ Format salah. Gunakan: <code>/account EMAIL</code>");
            return;
        }

        $email = implode(' ', $params);
        $account = Account::with(['tools', 'usageLogs', 'activityLogs'])
            ->where('email', 'LIKE', "%{$email}%")
            ->first();

        if (!$account) {
            $this->telegramService->sendMessage($chatId, "❌ Account tidak ditemukan.");
            return;
        }

        $message = $this->formatter->formatAccountDetail($account);
        $this->telegramService->sendMessage($chatId, $message);
    }

    private function handleUsage(int $chatId, TelegramUser $telegramUser): void
    {
        if (!$this->requireAuth($chatId, $telegramUser)) return;

        $usageLogs = UsageLog::with(['account.tools', 'tool'])
            ->whereDate('tanggal', today())
            ->latest('created_at')
            ->get();

        $message = $this->formatter->formatUsageLogs($usageLogs);
        $this->telegramService->sendMessage($chatId, $message);
    }

    private function handleActivity(int $chatId, TelegramUser $telegramUser): void
    {
        if (!$this->requireAuth($chatId, $telegramUser)) return;

        $activities = ActivityLog::with(['account', 'tool'])
            ->latest('waktu')
            ->limit(10)
            ->get();

        $message = $this->formatter->formatActivityLogs($activities);
        $this->telegramService->sendMessage($chatId, $message);
    }

    private function handleLog(int $chatId, TelegramUser $telegramUser, array $params): void
    {
        if (!$this->requireAuth($chatId, $telegramUser)) return;

        // Interactive log dengan inline keyboard
        $accounts = Account::with('tools')->get();
        
        if ($accounts->isEmpty()) {
            $this->telegramService->sendMessage($chatId, "❌ Belum ada account.");
            return;
        }

        // Show accounts as inline keyboard
        $buttons = [];
        foreach ($accounts as $account) {
            $buttons[] = [
                ['text' => $account->email, 'callback_data' => "log_account_{$account->id}"]
            ];
        }

        $this->telegramService->sendInlineKeyboard(
            $chatId,
            "📝 <b>Pilih Account untuk Log Aktivitas:</b>",
            $buttons
        );
    }

    private function handleQuick(int $chatId, TelegramUser $telegramUser, array $params): void
    {
        if (!$this->requireAuth($chatId, $telegramUser)) return;

        // Format: /quick akun01@gmail Kiro Dipakai
        if (count($params) < 3) {
            $this->telegramService->sendMessage(
                $chatId,
                "❌ Format salah.\n\n" .
                "Gunakan: <code>/quick EMAIL TOOL AKTIVITAS</code>\n" .
                "Contoh: <code>/quick akun01@gmail.com Kiro Dipakai</code>"
            );
            return;
        }

        $email = $params[0];
        $toolName = $params[1];
        $aktivitas = implode(' ', array_slice($params, 2));

        $account = Account::where('email', 'LIKE', "%{$email}%")->first();
        $tool = Tool::where('nama', 'LIKE', "%{$toolName}%")->first();

        if (!$account || !$tool) {
            $this->telegramService->sendMessage($chatId, "❌ Account atau Tool tidak ditemukan.");
            return;
        }

        $this->activityLogService->createActivityLog([
            'account_id' => $account->id,
            'tool_id' => $tool->id,
            'waktu' => now(),
            'aktivitas' => $aktivitas,
            'keterangan' => "Via Telegram Bot",
        ]);

        $this->telegramService->sendMessage(
            $chatId,
            "✅ <b>Log aktivitas berhasil ditambahkan!</b>\n\n" .
            "Account: {$account->email}\n" .
            "Tool: {$tool->nama}\n" .
            "Aktivitas: {$aktivitas}"
        );
    }

    private function handleUpdate(int $chatId, TelegramUser $telegramUser, array $params): void
    {
        if (!$this->requireAuth($chatId, $telegramUser)) return;

        // Format: /update akun01@gmail Kiro 3.6/50
        if (count($params) < 3) {
            $this->telegramService->sendMessage(
                $chatId,
                "❌ Format salah.\n\n" .
                "Gunakan: <code>/update EMAIL TOOL USED/TOTAL</code>\n" .
                "Contoh: <code>/update akun01@gmail.com Kiro 3.6/50</code>"
            );
            return;
        }

        $email = $params[0];
        $toolName = $params[1];
        $usage = $params[2];

        if (!str_contains($usage, '/')) {
            $this->telegramService->sendMessage($chatId, "❌ Format usage salah. Gunakan: USED/TOTAL");
            return;
        }

        list($used, $total) = explode('/', $usage);

        $account = Account::with('tools')->where('email', 'LIKE', "%{$email}%")->first();
        $tool = Tool::where('nama', 'LIKE', "%{$toolName}%")->first();

        if (!$account || !$tool) {
            $this->telegramService->sendMessage($chatId, "❌ Account atau Tool tidak ditemukan.");
            return;
        }

        $status = $this->usageLogService->autoDetectStatus((float)$used, (float)$total);

        $this->usageLogService->createUsageLog([
            'account_id' => $account->id,
            'tool_id' => $tool->id,
            'tanggal' => today(),
            'limit_used' => (float)$used,
            'limit_total' => (float)$total,
            'status' => $status,
            'catatan' => "Via Telegram Bot",
        ]);

        $percentage = ((float)$used / (float)$total) * 100;
        $statusEmoji = match($status) {
            'Ready' => '🟢',
            'Warning' => '🟡',
            'Limit' => '🔴',
            'Sedang Dipakai' => '🔵',
            default => '⚪',
        };

        // Get next reset info from pivot table
        $accountTool = $account->tools->find($tool->id);
        $resetInfo = "";
        
        if ($accountTool && $accountTool->pivot->next_reset) {
            $nextReset = \Carbon\Carbon::parse($accountTool->pivot->next_reset);
            $daysUntilReset = (int) now()->diffInDays($nextReset, false);
            
            if ($daysUntilReset > 0) {
                $resetInfo = "\n🔄 Reset: {$nextReset->format('d/m/Y')} ({$daysUntilReset} hari lagi)";
            } elseif ($daysUntilReset === 0) {
                $resetInfo = "\n🔄 Reset: Hari ini!";
            } else {
                $resetInfo = "\n⚠️ Reset sudah lewat! Update tanggal reset.";
            }
        } else {
            $resetInfo = "\n💡 Tip: Set tanggal reset di web app";
        }

        $this->telegramService->sendMessage(
            $chatId,
            "✅ <b>Usage log berhasil diupdate!</b>\n\n" .
            "Account: {$account->email}\n" .
            "Tool: {$tool->nama}\n" .
            "Usage: {$used}/{$total} (" . number_format($percentage, 1) . "%)\n" .
            "Status: {$statusEmoji} {$status}" .
            $resetInfo
        );
    }

    private function handleReset(int $chatId, TelegramUser $telegramUser, array $params): void
    {
        if (!$this->requireAuth($chatId, $telegramUser)) return;

        // Format: /reset akun01@gmail Kiro 15/08/2026
        if (count($params) < 3) {
            $this->telegramService->sendMessage(
                $chatId,
                "❌ Format salah.\n\n" .
                "Gunakan: <code>/reset EMAIL TOOL TANGGAL</code>\n" .
                "Contoh: <code>/reset akun01@gmail.com Kiro 15/08/2026</code>\n\n" .
                "Format tanggal: DD/MM/YYYY"
            );
            return;
        }

        $email = $params[0];
        $toolName = $params[1];
        $dateStr = $params[2];

        // Parse date
        try {
            $date = \Carbon\Carbon::createFromFormat('d/m/Y', $dateStr);
        } catch (\Exception $e) {
            $this->telegramService->sendMessage(
                $chatId, 
                "❌ Format tanggal salah.\n\nGunakan format: DD/MM/YYYY\nContoh: 15/08/2026"
            );
            return;
        }

        $account = Account::with('tools')->where('email', 'LIKE', "%{$email}%")->first();
        $tool = Tool::where('nama', 'LIKE', "%{$toolName}%")->first();

        if (!$account || !$tool) {
            $this->telegramService->sendMessage($chatId, "❌ Account atau Tool tidak ditemukan.");
            return;
        }

        // Update pivot table
        $account->tools()->updateExistingPivot($tool->id, [
            'next_reset' => $date
        ]);

        $daysUntilReset = (int) now()->diffInDays($date, false);
        $resetInfo = $daysUntilReset > 0 
            ? "({$daysUntilReset} hari lagi)"
            : ($daysUntilReset === 0 ? "(Hari ini)" : "(Sudah lewat)");

        $this->telegramService->sendMessage(
            $chatId,
            "✅ <b>Tanggal reset berhasil diupdate!</b>\n\n" .
            "Account: {$account->email}\n" .
            "Tool: {$tool->nama}\n" .
            "Reset: {$date->format('d/m/Y')} {$resetInfo}"
        );
    }

    private function handleNotify(int $chatId, TelegramUser $telegramUser, array $params): void
    {
        if (!$this->requireAuth($chatId, $telegramUser)) return;

        if (empty($params[0])) {
            $status = $telegramUser->notifications_enabled ? '✅ ON' : '❌ OFF';
            $this->telegramService->sendMessage(
                $chatId,
                "🔔 <b>Status Notifikasi:</b> {$status}\n\n" .
                "Gunakan:\n" .
                "• <code>/notify on</code> untuk mengaktifkan\n" .
                "• <code>/notify off</code> untuk menonaktifkan"
            );
            return;
        }

        $action = strtolower($params[0]);
        
        if ($action === 'on') {
            $telegramUser->notifications_enabled = true;
            $telegramUser->save();
            $this->telegramService->sendMessage($chatId, "✅ Notifikasi diaktifkan!");
        } elseif ($action === 'off') {
            $telegramUser->notifications_enabled = false;
            $telegramUser->save();
            $this->telegramService->sendMessage($chatId, "✅ Notifikasi dinonaktifkan!");
        } else {
            $this->telegramService->sendMessage($chatId, "❌ Parameter tidak valid. Gunakan: on atau off");
        }
    }

    private function handleUnknown(int $chatId): void
    {
        $this->telegramService->sendMessage(
            $chatId,
            "❌ Command tidak dikenali.\n\nGunakan /help untuk melihat daftar command."
        );
    }
}
