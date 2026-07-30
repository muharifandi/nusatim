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
            // 4th optional scope column, alongside service_id/partner_id/
            // partner_project_id - matches Partner::LEVELS ('bronze'|
            // 'silver'|'gold'|'platinum'). Priority: project > partner >
            // level > service > global - see CommissionScheme::resolveFor().
            $table->string('level')->nullable()->after('partner_project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commission_schemes', function (Blueprint $table) {
            $table->dropColumn('level');
        });
    }
};
