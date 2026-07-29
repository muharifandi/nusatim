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
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);

            // Snapshotted from the partner at submit time (not a live
            // reference) - if the partner changes their bank details later,
            // old withdrawal records should still show what was used then.
            $table->string('bank_name');
            $table->string('bank_account_number');
            $table->string('bank_account_holder');

            // Required on every withdrawal request, distinct from the KTP
            // uploaded once at registration (partners.ktp_path).
            $table->string('ktp_path');
            $table->string('proof_of_transfer_path')->nullable();

            $table->text('note')->nullable();
            $table->string('status')->default('pending'); // pending|approved|paid|rejected
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
