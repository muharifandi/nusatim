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
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->string('url');
            $table->string('ip_address', 45)->nullable();
            $table->string('country_code', 2)->nullable();
            $table->string('country_name')->nullable();
            $table->string('referrer')->nullable();
            $table->string('user_agent')->nullable();
            $table->foreignId('post_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('viewed_at');

            $table->index('viewed_at');
            $table->index('path');
            $table->index('country_code');
            $table->index(['ip_address', 'country_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
