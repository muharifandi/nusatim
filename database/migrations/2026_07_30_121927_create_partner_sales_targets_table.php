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
        Schema::create('partner_sales_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            // Always normalized to the 1st of the month - one target row per
            // partner per calendar month, not an arbitrary date range.
            $table->date('period');
            $table->decimal('target_amount', 15, 2);
            $table->timestamps();

            $table->unique(['partner_id', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_sales_targets');
    }
};
