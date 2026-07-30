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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('auditable'); // model yang diaudit (Partner, Lead, dst)
            $table->nullableMorphs('user'); // aktor (App\Models\User atau App\Models\Partner)
            $table->string('action'); // created|updated|deleted
            $table->json('changes')->nullable(); // diff before/after, cuma diisi untuk 'updated'
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
