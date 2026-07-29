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
        Schema::create('partner_projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            // "Produk" - reuses the existing services catalog, same as leads/customers.
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->decimal('budget', 15, 2)->nullable();
            $table->string('location')->nullable();
            $table->date('deadline')->nullable();
            $table->string('difficulty')->nullable(); // low|medium|high
            // Preview/reference number shown to partners browsing the board -
            // NOT a real commission calculation. The actual Commission Scheme
            // engine (Percentage/Recurring Percentage/Flat) is a separate,
            // not-yet-built system (Fase 9/18/19).
            $table->decimal('commission_value', 15, 2)->nullable();
            $table->string('status')->default('draft'); // draft|available|pending_approval|assigned|in_progress|closed|cancelled
            $table->foreignId('partner_id')->nullable()->constrained()->nullOnDelete();
            // Populated by the Fase 8 UI later - column exists now since it's
            // cheap to add alongside the rest of the schema.
            $table->unsignedTinyInteger('progress')->default(0);
            $table->timestamp('claimed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_projects');
    }
};
