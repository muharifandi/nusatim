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
        Schema::create('marketing_materials', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category'); // see MarketingMaterial::CATEGORIES
            $table->text('description')->nullable();
            // Exactly one of these two is filled, depending on whether the
            // category is file-based (brochure, banner, video, ...) or
            // text-based (WA/email template, FAQ, selling point) - see
            // MarketingMaterial::FILE_CATEGORIES.
            $table->string('file_path')->nullable();
            $table->text('content')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_materials');
    }
};
