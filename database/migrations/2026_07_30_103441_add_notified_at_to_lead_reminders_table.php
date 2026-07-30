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
        Schema::table('lead_reminders', function (Blueprint $table) {
            // Marks when the due-reminder notification was sent, so the
            // scheduled reminders:notify-due command never notifies the
            // same reminder twice.
            $table->timestamp('notified_at')->nullable()->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lead_reminders', function (Blueprint $table) {
            $table->dropColumn('notified_at');
        });
    }
};
