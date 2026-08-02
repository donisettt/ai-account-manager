<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->foreignId('tool_id')->constrained()->onDelete('cascade');
            $table->timestamp('waktu'); // Waktu aktivitas dengan jam:menit
            $table->enum('aktivitas', ['Dipakai', 'Limit', 'Reset', 'Login', 'Logout', 'Error', 'Maintenance'])->default('Dipakai');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            // Index untuk performa query
            $table->index(['account_id', 'waktu']);
            $table->index(['tool_id', 'waktu']);
            $table->index(['waktu']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
