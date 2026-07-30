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
        Schema::create('workflow_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('workflow')->unique(); // salah satu App\Models\WorkflowAssignment::WORKFLOWS
            // Null berarti "siapa saja yang punya permission approve di
            // modul terkait boleh approve" - diisi berarti tambahan syarat
            // harus juga punya Role ini.
            $table->foreignId('role_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_assignments');
    }
};
