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
        Schema::table('partner_settings', function (Blueprint $table) {
            // Minimal slice of Fase 23 (Partner Settings) pulled forward
            // because Fase 10 (Withdrawal) needs it - not the full module.
            $table->decimal('minimum_withdrawal', 15, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partner_settings', function (Blueprint $table) {
            $table->dropColumn('minimum_withdrawal');
        });
    }
};
