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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            // Nullable + unique: a customer almost always comes from a Won
            // lead (Lead::markWon() firstOrCreate's this), but the column
            // stays nullable in case a customer record ever needs to exist
            // without a lead history. Unique so markWon() is idempotent.
            $table->foreignId('lead_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('name');
            // "Data PIC customer" and "Data Kontak" in the spec are treated
            // as the same contact person, not two separate entities.
            $table->string('pic_name')->nullable();
            $table->string('pic_phone')->nullable();
            $table->string('pic_email')->nullable();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->decimal('project_value', 15, 2)->nullable();
            $table->string('payment_status')->default('unpaid'); // unpaid|partial|paid
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
