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
                'message_text' => $update->getMessage() ? $update->getMessage()->getText() : null
            ]);

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
}
