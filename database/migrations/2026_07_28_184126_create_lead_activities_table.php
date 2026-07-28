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
        Schema::create('lead_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained()->cascadeOnDelete();
            // No FK constraint here on purpose: the `customers` table is
            // created in a later migration (Customer is sourced from a Won
            // Lead), so this stays a plain indexed column rather than
            // reordering migrations around a circular dependency.
            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->string('type'); // created|note|status_change|document
            $table->text('body')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_activities');
    }
};
