<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class TestTelegramBot extends Command
{
    protected $signature = 'telegram:test';
    protected $description = 'Test Telegram Bot Connection';

    public function handle()
    {
        try {
            $me = Telegram::getMe();

            $this->info('✅ Bot berhasil terkoneksi!');
            $this->info('Bot Name: ' . $me->getFirstName());
            $this->info('Username: @' . $me->getUsername());

            // Get webhook info
            $webhookInfo = Telegram::getWebhookInfo();
            $this->info('Webhook URL: ' . ($webhookInfo->url ?: 'Not set'));

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
