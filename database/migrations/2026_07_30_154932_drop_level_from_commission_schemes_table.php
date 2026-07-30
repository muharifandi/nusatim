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
        Schema::table('commission_schemes', function (Blueprint $table) {
            // Final business decision (2026-07-30): Partner Level is NOT
            // part of Commission Scheme resolution - it's a purely
            // informational business attribute (badge/loyalty/reward/
            // dashboard). Reverts the 'level' scope column added earlier
            // the same day.
            $table->dropColumn('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commission_schemes', function (Blueprint $table) {
            $table->string('level')->nullable()->after('partner_project_id');
        });
    }
};
