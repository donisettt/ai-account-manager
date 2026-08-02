<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_users', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('telegram_id')->unique();
            $table->string('username')->nullable();
            $table->bigInteger('chat_id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('notifications_enabled')->default(true);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('auth_token')->nullable()->unique();
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamp('last_command_at')->nullable();
            $table->timestamps();
            
            $table->index(['telegram_id', 'is_active']);
        });

        // Add telegram_user_id ke users table untuk easy access
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('telegram_user_id')->nullable()->after('id')->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['telegram_user_id']);
            $table->dropColumn('telegram_user_id');
        });
        
        Schema::dropIfExists('telegram_users');
    }
};
