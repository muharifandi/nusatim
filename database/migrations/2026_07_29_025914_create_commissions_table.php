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
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            // Nullable: a pure bonus (is_bonus=true) can be awarded to a
            // partner without being tied to any specific customer. Not
            // unique at the DB level - Commission::generateForCustomer()
            // enforces "one regular commission per customer" itself
            // (scoped to is_bonus=false), since a customer can still have
            // extra bonus rows alongside its one regular commission.
            $table->foreignId('customer_id')->nullable()->constrained()->cascadeOnDelete();
            // Denormalized from customer.partner_id purely so admin/partner
            // queries and scoping don't need a join through customers.
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('commission_scheme_id')->nullable()->constrained()->nullOnDelete();

            $table->decimal('project_value', 15, 2)->nullable();
            // No invoicing system exists yet - this is a snapshot equal to
            // project_value at generation time, not a real linked invoice.
            $table->decimal('invoice_value', 15, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable(); // snapshot from the scheme used
            $table->decimal('amount', 15, 2);
            $table->string('type'); // percentage|recurring_percentage|flat|bonus
            $table->string('status')->default('pending'); // pending|waiting_client_payment|approved|paid|rejected
            $table->text('rejection_reason')->nullable();
            $table->boolean('is_bonus')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
