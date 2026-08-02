<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class SetupTelegramWebhook extends Command
{
    protected $signature = 'telegram:setup-webhook';
    protected $description = 'Setup Telegram Bot Webhook';

    public function handle()
    {
        $webhookUrl = env('TELEGRAM_WEBHOOK_URL') . '/' . env('TELEGRAM_WEBHOOK_SECRET');

        try {
            $response = Telegram::setWebhook([
                'url' => $webhookUrl,
            ]);

            if ($response) {
                $this->info('✅ Webhook berhasil di-setup!');
                $this->info('URL: ' . $webhookUrl);
            } else {
                $this->error('❌ Gagal setup webhook');
            }

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
