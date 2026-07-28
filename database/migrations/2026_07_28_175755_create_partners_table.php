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
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');

            $table->string('status')->default('pending_review'); // pending_review|approved|rejected
            // Placeholder until "Level Partner" is actually defined by the
            // spec owner (see todo_partnert.md Fase 0) - free text set only
            // from the admin side for now, not chosen by the partner.
            $table->string('level')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();

            // Registration documents/bank info kept as plain columns rather
            // than separate tables: this spec has no case of a partner
            // needing more than one of each, so a join would add nothing.
            $table->string('profile_photo_path')->nullable();
            $table->string('ktp_path')->nullable();
            $table->string('npwp_path')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_holder')->nullable();
            $table->timestamp('agreement_accepted_at')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
