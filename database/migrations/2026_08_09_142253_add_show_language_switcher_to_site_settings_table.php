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
        Schema::table('site_settings', function (Blueprint $table) {
            // The nav's language switcher only has "Indonesia" (active) and
            // "English (Segera Hadir/disabled)" right now - no real i18n
            // exists yet, so it's decorative and confusing. Off by default;
            // flip on once English is actually implemented.
            $table->boolean('show_language_switcher')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('show_language_switcher');
        });
    }
};
