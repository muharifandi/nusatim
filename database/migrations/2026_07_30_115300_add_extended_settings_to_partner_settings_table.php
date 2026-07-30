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
            // Explicit override, checked in CommissionScheme::resolveFor()
            // above the older "scope-less scheme = fallback" convention -
            // kept alongside it rather than replacing it, so existing
            // scope-less schemes still work if this is left unset.
            $table->foreignId('default_commission_scheme_id')->nullable()->constrained('commission_schemes')->nullOnDelete();

            // Project Claim Rule
            $table->unsignedInteger('max_concurrent_claimed_projects')->nullable();
            $table->unsignedInteger('claim_processing_hours')->nullable();

            // Workflow Approval - free-text policy documentation, not a real
            // role/permission system (none exists yet - see todo_partnert.md
            // Fase 0's still-open "siapa approve apa" question).
            $table->text('approval_workflow_notes')->nullable();

            // Notification channel default for newly-registered partners
            // (Register::handleRegistration() reads this instead of relying
            // on the column default). Per-message template editing is not
            // part of this column - see todo_partnert.md Fase 23 notes.
            $table->boolean('default_email_notifications_enabled')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partner_settings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('default_commission_scheme_id');
            $table->dropColumn([
                'max_concurrent_claimed_projects',
                'claim_processing_hours',
                'approval_workflow_notes',
                'default_email_notifications_enabled',
            ]);
        });
    }
};
