<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Services\Telegram\{TelegramService, CommandHandler};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class WebhookController extends Controller
{
    public function __construct(
        private TelegramService $telegramService,
        private CommandHandler $commandHandler
    ) {}

    public function handle(Request $request, string $secret): JsonResponse
    {
        \Log::info('Telegram webhook received', [
            'secret_valid' => $secret === config('telegram.webhook_secret'),
            'body' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        // Verify secret
        if ($secret !== config('telegram.webhook_secret')) {
            \Log::warning('Telegram webhook unauthorized', ['secret' => $secret]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            // Get update directly from request
            $updateData = $request->all();
            
            \Log::info('Raw update data', ['data' => $updateData]);
            
            if (empty($updateData)) {
                \Log::warning('Empty update received');
                return response()->json(['ok' => true]);
            }

            // Create Update object manually
            $update = new Update($updateData);
            
            \Log::info('Update object created', [
                'update_id' => $update->getUpdateId(),
                'has_message' => $update->getMessage() !== null,
                'has_callback_query' => $update->getCallbackQuery() !== null,
                'message_text' => $update->getMessage() ? $update->getMessage()->getText() : null
            ]);

            // Handle callback query (inline keyboard clicks)
            if ($update->getCallbackQuery()) {
                \Log::info('Handling callback query');
                $this->handleCallbackQuery($update);
                return response()->json(['ok' => true]);
            }

            // Skip if not a message
            if (!$update->getMessage()) {
                \Log::info('Update skipped - not a message');
                return response()->json(['ok' => true]);
            }

            // Get or create telegram user
            $telegramUser = $this->telegramService->getOrCreateTelegramUser($update);
            
            \Log::info('Telegram user resolved', [
                'telegram_id' => $telegramUser->telegram_id,
                'user_id' => $telegramUser->user_id,
                'is_authenticated' => $telegramUser->isAuthenticated()
            ]);

            // Handle command
            $this->commandHandler->handle($update, $telegramUser);
            
            \Log::info('Command handled successfully');

            return response()->json(['ok' => true]);

        } catch (\Exception $e) {
            \Log::error('Telegram Webhook Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    private function handleCallbackQuery(Update $update): void
    {
        $callbackQuery = $update->getCallbackQuery();
        $data = $callbackQuery->getData();
        $chatId = $callbackQuery->getMessage()->getChat()->getId();
        $messageId = $callbackQuery->getMessage()->getMessageId();

        \Log::info('Processing callback query', [
            'data' => $data,
            'chat_id' => $chatId,
            'message_id' => $messageId
        ]);

        // Get telegram user
        $telegramId = $callbackQuery->getFrom()->getId();
        $telegramUser = \App\Models\TelegramUser::where('telegram_id', $telegramId)->first();

        if (!$telegramUser || !$telegramUser->isAuthenticated()) {
            Telegram::answerCallbackQuery([
                'callback_query_id' => $callbackQuery->getId(),
                'text' => '❌ Anda belum terautentikasi',
                'show_alert' => true
            ]);
            return;
        }

        // Parse callback data
        if (preg_match('/^log_account_(\d+)$/', $data, $matches)) {
            $accountId = $matches[1];
            $this->handleLogAccountCallback($accountId, $chatId, $callbackQuery->getId());
        } elseif (preg_match('/^log_tool_(\d+)_(\d+)$/', $data, $matches)) {
            $accountId = $matches[1];
            $toolId = $matches[2];
            $this->handleLogToolCallback($accountId, $toolId, $chatId, $callbackQuery->getId());
        } elseif (preg_match('/^log_activity_(\d+)_(\d+)_(.+)$/', $data, $matches)) {
            $accountId = $matches[1];
            $toolId = $matches[2];
            $aktivitas = $matches[3];
            $this->handleLogActivityCallback($accountId, $toolId, $aktivitas, $chatId, $callbackQuery->getId());
        } else {
            Telegram::answerCallbackQuery([
                'callback_query_id' => $callbackQuery->getId(),
                'text' => '❌ Callback tidak dikenali'
            ]);
        }
    }

    private function handleLogAccountCallback(int $accountId, int $chatId, string $callbackQueryId): void
    {
        $account = \App\Models\Account::with('tools')->find($accountId);

        if (!$account) {
            Telegram::answerCallbackQuery([
                'callback_query_id' => $callbackQueryId,
                'text' => '❌ Account tidak ditemukan',
                'show_alert' => true
            ]);
            return;
        }

        if ($account->tools->isEmpty()) {
            Telegram::answerCallbackQuery([
                'callback_query_id' => $callbackQueryId,
                'text' => '❌ Account ini belum memiliki tools',
                'show_alert' => true
            ]);
            return;
        }

        // Show tools as inline keyboard
        $buttons = [];
        foreach ($account->tools as $tool) {
            $buttons[] = [
                ['text' => $tool->nama, 'callback_data' => "log_tool_{$accountId}_{$tool->id}"]
            ];
        }

        Telegram::editMessageText([
            'chat_id' => $chatId,
            'message_id' => Telegram::getWebhookUpdate()->getCallbackQuery()->getMessage()->getMessageId(),
            'text' => "🛠️ <b>Pilih Tool untuk {$account->email}:</b>",
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode(['inline_keyboard' => $buttons])
        ]);

        Telegram::answerCallbackQuery([
            'callback_query_id' => $callbackQueryId
        ]);
    }

    private function handleLogToolCallback(int $accountId, int $toolId, int $chatId, string $callbackQueryId): void
    {
        $account = \App\Models\Account::find($accountId);
        $tool = \App\Models\Tool::find($toolId);

        if (!$account || !$tool) {
            Telegram::answerCallbackQuery([
                'callback_query_id' => $callbackQueryId,
                'text' => '❌ Data tidak ditemukan',
                'show_alert' => true
            ]);
            return;
        }

        // Show activities as inline keyboard
        $activities = ['Dipakai', 'Selesai', 'Limit', 'Reset', 'Error'];
        $buttons = [];
        foreach ($activities as $activity) {
            $emoji = match($activity) {
                'Dipakai' => '▶️',
                'Selesai' => '✅',
                'Limit' => '🔴',
                'Reset' => '🔄',
                'Error' => '⚠️',
                default => '•'
            };
            $buttons[] = [
                ['text' => "{$emoji} {$activity}", 'callback_data' => "log_activity_{$accountId}_{$toolId}_{$activity}"]
            ];
        }

        Telegram::editMessageText([
            'chat_id' => $chatId,
            'message_id' => Telegram::getWebhookUpdate()->getCallbackQuery()->getMessage()->getMessageId(),
            'text' => "✏️ <b>Pilih Aktivitas:</b>\n\n" .
                      "Account: {$account->email}\n" .
                      "Tool: {$tool->nama}",
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode(['inline_keyboard' => $buttons])
        ]);

        Telegram::answerCallbackQuery([
            'callback_query_id' => $callbackQueryId
        ]);
    }

    private function handleLogActivityCallback(int $accountId, int $toolId, string $aktivitas, int $chatId, string $callbackQueryId): void
    {
        $account = \App\Models\Account::find($accountId);
        $tool = \App\Models\Tool::find($toolId);

        if (!$account || !$tool) {
            Telegram::answerCallbackQuery([
                'callback_query_id' => $callbackQueryId,
                'text' => '❌ Data tidak ditemukan',
                'show_alert' => true
            ]);
            return;
        }

        // Create activity log
        $activityLogService = app(\App\Services\ActivityLog\ActivityLogService::class);
        $activityLogService->createActivityLog([
            'account_id' => $accountId,
            'tool_id' => $toolId,
            'waktu' => now(),
            'aktivitas' => $aktivitas,
            'keterangan' => 'Via Telegram Bot (Interactive)'
        ]);

        $emoji = match($aktivitas) {
            'Dipakai' => '▶️',
            'Selesai' => '✅',
            'Limit' => '🔴',
            'Reset' => '🔄',
            'Error' => '⚠️',
            default => '•'
        };

        Telegram::editMessageText([
            'chat_id' => $chatId,
            'message_id' => Telegram::getWebhookUpdate()->getCallbackQuery()->getMessage()->getMessageId(),
            'text' => "✅ <b>Log aktivitas berhasil ditambahkan!</b>\n\n" .
                      "Account: {$account->email}\n" .
                      "Tool: {$tool->nama}\n" .
                      "Aktivitas: {$emoji} {$aktivitas}\n" .
                      "Waktu: " . now()->format('d/m/Y H:i'),
            'parse_mode' => 'HTML'
        ]);

        Telegram::answerCallbackQuery([
            'callback_query_id' => $callbackQueryId,
            'text' => '✅ Berhasil!'
        ]);
    }
}
