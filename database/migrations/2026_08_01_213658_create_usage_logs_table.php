<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->foreignId('tool_id')->constrained()->onDelete('cascade');
            $table->date('tanggal');
            $table->decimal('limit_used', 10, 2)->default(0); // Contoh: 3.6
            $table->decimal('limit_total', 10, 2)->default(50); // Contoh: 50
            $table->enum('status', ['Ready', 'Warning', 'Limit', 'Maintenance', 'Sedang Dipakai'])->default('Ready');
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            // Index untuk performa
            $table->index(['account_id', 'tanggal']);
            $table->index(['tool_id', 'tanggal']);
        });

        // Tambah field last_used_at di table accounts
        Schema::table('accounts', function (Blueprint $table) {
            $table->timestamp('last_used_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('last_used_at');
        });
        
        Schema::dropIfExists('usage_logs');
    }
};
