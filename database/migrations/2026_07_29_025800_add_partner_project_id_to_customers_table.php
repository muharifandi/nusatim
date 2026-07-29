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
        Schema::table('customers', function (Blueprint $table) {
            // Links a Customer back to the PartnerProject it came from, for
            // deals sourced via Project Board claim (Fase 7) rather than a
            // Won Lead (Fase 3). Nullable+unique: not every customer has one,
            // and a project can only ever produce one customer. This is what
            // lets Commission Scheme's "Per Project" scope (Fase 18) actually
            // match against something - see PartnerProject::approveClaim().
            $table->foreignId('partner_project_id')->nullable()->unique()->after('lead_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('partner_project_id');
        });
    }
};
