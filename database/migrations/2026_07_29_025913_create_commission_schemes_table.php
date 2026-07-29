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
        Schema::create('commission_schemes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // percentage|recurring_percentage|flat
            $table->decimal('percentage', 5, 2)->nullable(); // percentage|recurring_percentage
            $table->decimal('flat_amount', 15, 2)->nullable(); // flat

            // Each scheme is expected to use at most ONE of these three scope
            // columns (an assumption - the spec never states what happens if
            // more than one is set). Match priority when generating a
            // commission is: project > partner > service > global (all
            // three null) - see CommissionScheme::resolveFor().
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->foreignId('partner_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('partner_project_id')->nullable()->constrained()->nullOnDelete();

            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_schemes');
    }
};
