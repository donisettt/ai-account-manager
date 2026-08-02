<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->text('password'); // Encrypted, bukan hashed
            $table->string('provider')->default('Google'); // Google, Microsoft, Custom, etc
            $table->string('provider_login')->nullable(); // URL atau method login
            $table->string('recovery_email')->nullable();
            $table->enum('status', ['Ready', 'In Use', 'Suspended', 'Expired'])->default('Ready');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // Pivot table untuk relasi many-to-many
        Schema::create('account_tool', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->foreignId('tool_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_tool');
        Schema::dropIfExists('accounts');
    }
};
