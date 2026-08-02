<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('telegram_users', function (Blueprint $table) {
            // Make telegram_id and chat_id nullable
            // They will be filled when user runs /auth command
            $table->bigInteger('telegram_id')->nullable()->change();
            $table->bigInteger('chat_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('telegram_users', function (Blueprint $table) {
            $table->bigInteger('telegram_id')->nullable(false)->change();
            $table->bigInteger('chat_id')->nullable(false)->change();
        });
    }
};
