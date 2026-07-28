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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            // "Produk" - reuses the existing services catalog per Fase 0's
            // recommendation (still pending final confirmation from the
            // spec owner, see todo_partnert.md).
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->decimal('estimated_value', 15, 2)->nullable();
            $table->string('status')->default('new'); // new|contacted|qualified|opportunity|proposal|negotiation|won|lost
            $table->boolean('is_validated')->default(false); // set by admin, Fase 17
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
