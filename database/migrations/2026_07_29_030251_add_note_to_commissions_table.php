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
        Schema::table('commissions', function (Blueprint $table) {
            // Free-text note, distinct from rejection_reason (e.g. why an
            // admin-added bonus was given). Added separately because the
            // need for it only became clear while wiring up the "Bonus
            // Komisi" admin action.
            $table->text('note')->nullable()->after('rejection_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            $table->dropColumn('note');
        });
    }
};
