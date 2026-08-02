<?php

namespace App\Services\Telegram;

use App\Models\TelegramUser;
use App\Models\User;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\Update;

class TelegramService
{
    public function sendMessage(int $chatId, string $text, $keyboard = null): void
    {
        try {
            $params = [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
            ];

            if ($keyboard) {
                $params['reply_markup'] = $keyboard;
            }

            \Log::info('Sending telegram message', [
                'chat_id' => $chatId,
                'text_length' => strlen($text),
                'has_keyboard' => $keyboard !== null
            ]);

            $response = Telegram::sendMessage($params);
            
            \Log::info('Telegram message sent successfully', [
                'message_id' => $response->getMessageId()
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send telegram message', [
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function sendInlineKeyboard(int $chatId, string $text, array $buttons): void
    {
        $keyboard = Keyboard::make([
            'inline_keyboard' => $buttons
        ]);

        $this->sendMessage($chatId, $text, $keyboard);
    }

    public function getOrCreateTelegramUser(Update $update): TelegramUser
    {
        $message = $update->getMessage();
        $from = $message->getFrom();

        return TelegramUser::updateOrCreate(
            ['telegram_id' => $from->getId()],
            [
                'username' => $from->getUsername(),
                'chat_id' => $message->getChat()->getId(),
                'first_name' => $from->getFirstName(),
                'last_name' => $from->getLastName(),
                'is_active' => true,
            ]
        );
    }

    public function linkUserToTelegram(string $token, int $telegramUserId): bool
    {
        // Find record by token
        $telegramUserByToken = TelegramUser::where('auth_token', $token)
            ->where('token_expires_at', '>', now())
            ->first();

        if (!$telegramUserByToken || !$telegramUserByToken->user_id) {
            \Log::warning('Token not found or no user_id', [
                'token' => $token,
                'telegram_user_id' => $telegramUserId
            ]);
            return false;
        }

        $userId = $telegramUserByToken->user_id;

        // Get telegram user record for this telegram_id (created by getOrCreateTelegramUser)
        $telegramUser = TelegramUser::where('telegram_id', $telegramUserId)->first();
        
        if ($telegramUser && $telegramUser->id !== $telegramUserByToken->id) {
            // Already exists with actual telegram_id, update it
            $telegramUser->user_id = $userId;
            $telegramUser->auth_token = null;
            $telegramUser->token_expires_at = null;
            $telegramUser->is_active = true;
            $telegramUser->save();
            
            // Delete the temporary token record
            $telegramUserByToken->delete();
            
            \Log::info('Linked existing telegram user', [
                'telegram_user_id' => $telegramUser->id,
                'user_id' => $userId
            ]);
        } else {
            // Update the temporary record with actual telegram_id
            $telegramUserByToken->telegram_id = $telegramUserId;
            $telegramUserByToken->auth_token = null;
            $telegramUserByToken->token_expires_at = null;
            $telegramUserByToken->is_active = true;
            $telegramUserByToken->save();
            
            $telegramUser = $telegramUserByToken;
            
            \Log::info('Updated token record with telegram_id', [
                'telegram_user_id' => $telegramUser->id,
                'user_id' => $userId
            ]);
        }

        // Update user table
        $user = User::find($userId);
        if ($user) {
            $user->telegram_user_id = $telegramUser->id;
            $user->save();
            
            \Log::info('Updated user.telegram_user_id', [
                'user_id' => $userId,
                'telegram_user_id' => $telegramUser->id
            ]);
        }

        return true;
    }

    public function sendNotification(int $userId, string $message): void
    {
        $user = User::find($userId);
        
        if (!$user || !$user->telegram_user_id) {
            return;
        }

        $telegramUser = TelegramUser::find($user->telegram_user_id);
        
        if (!$telegramUser || !$telegramUser->notifications_enabled) {
            return;
        }

        $this->sendMessage($telegramUser->chat_id, "🔔 <b>Notifikasi</b>\n\n" . $message);
    }
}
