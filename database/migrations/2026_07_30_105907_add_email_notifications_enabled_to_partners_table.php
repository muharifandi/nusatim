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
        Schema::table('partners', function (Blueprint $table) {
            // In-app (database) notifications from Fase 13 are always on -
            // this only gates the extra transactional emails sent alongside
            // some of them (see PartnerProject::approveClaim()/rejectClaim()).
            $table->boolean('email_notifications_enabled')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn('email_notifications_enabled');
        });
    }
};
