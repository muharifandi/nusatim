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
            // Superseded by RBAC + Workflow Assignment (Fase 24/25) - this
            // was a free-text placeholder for "who approves what".
            $table->dropColumn('approval_workflow_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partner_settings', function (Blueprint $table) {
            $table->text('approval_workflow_notes')->nullable();
        });
    }
};
