<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('account_tool', function (Blueprint $table) {
            $table->date('next_reset')->nullable()->after('tool_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_tool', function (Blueprint $table) {
            $table->dropColumn('next_reset');
        });
    }
};
